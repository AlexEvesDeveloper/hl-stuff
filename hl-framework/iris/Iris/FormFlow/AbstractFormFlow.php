<?php

namespace Iris\FormFlow;

use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\FormFlow\Exception\FormFlowStepAlreadyExistsException;
use Iris\FormFlow\Exception\FormFlowStepNotFoundException;
use Iris\FormFlow\Exception\ReferrerNotFoundException;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Iris\FormFlow\Exception\ProductNotFoundOnApplicationException;
use Iris\Referencing\FormFlow\Exception\StepCollectionNotArrayException;
use Iris\Referencing\FormFlow\Exception\StepIsNotCorrectTypeException;
use Iris\FormFlow\Exception\StepUrlNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractFormFlow
 *
 * @package Iris\FormFlow
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractFormFlow
{
    /**
     * Referrer before and after ids
     */
    const REFERRER_BEFORE = 'before';
    const REFERRER_AFTER = 'after';

    /**
     * @var array
     */
    protected $steps = array();

    /**
     * @var ProgressiveStoreInterface
     */
    protected $progressiveStore;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Step URLs
     */
    static public $urls = array();

    /**
     * @var string
     *
     * @todo This is not the responsibility of the form flow handler, this must be moved to a dedicated service
     */
    protected $canAgentCompleteProspectiveLandlord;

    /**
     * Constructor
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param ProgressiveStoreInterface $progressiveStore
     */
    final public function __construct(Request $request, ProgressiveStoreInterface $progressiveStore)
    {
        $this->request = $request;
        $this->progressiveStore = $progressiveStore;

        $this->initialise();
    }

    /**
     * Get step URL by enumeration
     *
     * @param int $enumeration
     * @throws \Iris\FormFlow\Exception\StepUrlNotFoundException
     * @return string
     */
    protected function getStepUrlByEnum($enumeration)
    {
        // Strip off any query params and append to URL
        $query = '';
        if (preg_match('/^.*(\?.*)$/', $enumeration, $matches)) {
            $query = $matches[1];
            $enumeration = str_replace($query, '', $enumeration);
        }

        $urls = $this->getStepUrls();

        if (!isset($urls[$enumeration])) {
            throw new StepUrlNotFoundException(sprintf('Step URL could not be found for enumeration %s', $enumeration));
        }

        return $urls[$enumeration] . $query;
    }

    /**
     * Get step URLs
     *
     * @return array
     */
    protected function getStepUrls()
    {
        $clazz = get_called_class();
        return $clazz::$urls;
    }

    /**
     * Add a form flow step
     *
     * @param FormFlowStepInterface $step
     * @return $this
     * @throws Exception\FormFlowStepAlreadyExistsException
     */
    public function addFormFlowStep(FormFlowStepInterface $step)
    {
        $url = $this->getStepUrlByEnum($step->getUrl());

        if (isset($this->steps[$url])) {
            throw new FormFlowStepAlreadyExistsException(
                sprintf('Form flow step with the URL %s already exists', $url)
            );
        }

        $this->steps[$url] = $step;

        return $this;
    }

    /**
     * Add a collection of form flow steps to this form flow. Closure must return an
     * array of FormFlowStepInterface
     *
     * <code>
     * $formFlow
     *     ->addFormFlowStepCollection(function (AbstractFormFlow $flow) {
     *         return array(
     *             new FormFlowStep(
     *                  '/url/path/current',
     *                  function (AbstractFormFlow $flow) {
     *                      return '/url/path/previous';
     *                  },
     *                  function (AbstractFormFlow $flow) {
     *                      return '/url/path/next';
     *                  }
     *             ),
     *             // ...
     *         );
     *     })
     * ;
     * </code>
     *
     * @param callable $stepCollectionBuilder
     * @throws \Iris\Referencing\FormFlow\Exception\StepIsNotCorrectTypeException
     * @throws \Iris\Referencing\FormFlow\Exception\StepCollectionNotArrayException
     * @return $this
     */
    public function addFormFlowStepCollection(\Closure $stepCollectionBuilder)
    {
        $steps = $stepCollectionBuilder($this);

        if (!is_array($steps)) {
            throw new StepCollectionNotArrayException(
                sprintf('Step collection builder closure must return an array, %s given instead', gettype($steps))
            );
        }

        /** @var \Iris\FormFlow\FormFlowStepInterface $step */
        foreach ($steps as $position => $step) {

            if (!($step instanceof FormFlowStepInterface)) {
                throw new StepIsNotCorrectTypeException(
                    sprintf(
                        'Step at position [%s] in collection is not of type FormFlowStepInterface, %s given instead',
                        $position,
                        gettype($step)
                    )
                );
            }

            $this->addFormFlowStep($step);
        }

        return $this;
    }

    /**
     * Get a form flow step by URL
     *
     * @param string $url
     * @throws Exception\FormFlowStepNotFoundException
     * @return FormFlowStepInterface
     */
    public function getFormFlowStepByUrl($url)
    {
        if (!isset($this->steps[$url])) {
            throw new FormFlowStepNotFoundException(sprintf('Form flow step not found with URL %s', $url));
        }

        return $this->steps[$url];
    }

    /**
     * Get the 'back' URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        $step = $this->getFormFlowStepByUrl($this->getRequestUrlPath());

        $builder = $step->getBackUrlBuilder();

        $enum = $builder($this);

        return $this->getStepUrlByEnum($enum);
    }

    /**
     * Get the 'next' URL
     *
     * @return string
     */
    public function getNextUrl()
    {
        $step = $this->getFormFlowStepByUrl($this->getRequestUrlPath());

        $builder = $step->getNextUrlBuilder();

        $enum = $builder($this);

        return $this->getStepUrlByEnum($enum);
    }

    /**
     * Run form flow - should be run before each
     * action is executed
     *
     * @return void
     */
    public function run()
    {
        $this->resolverSkipStep();
    }

    /**
     * Resolve the step skipping closure
     *
     * @throws Exception\ReferrerNotFoundException
     * @return void
     */
    protected function resolverSkipStep()
    {
        try {
            $step = $this->getFormFlowStepByUrl($this->getRequestUrlPath());
        }
        catch (FormFlowStepNotFoundException $e) {
            return;
        }

        $skipper = $step->getSkip();

        $canSkip = false;

        if ($skipper instanceof \Closure) {
            $canSkip = $skipper($this);
        }

        if (!$canSkip) {
            return;
        }

        $referrer = $this->getReferrerPath(
            $this->request->server->get('HTTP_REFERER')
        );

        $currentUrl = $this->getRequestUrlPath();

        if (!$referrer) {
            throw new ReferrerNotFoundException(sprintf('Unable to find HTTP_REFERER for request %s', $currentUrl));
        }

        $currentPosition = array_search($currentUrl, array_keys($this->steps));

        $location = null;

        if (false !== $currentPosition) {

            // We have a position, take an upper and lower slice
            $lower = array_slice($this->steps, 0, $currentPosition);
            $upper = array_slice($this->steps, $currentPosition);

            // Is the referrer in the upper or lower slice?
            if (isset($lower[$referrer])) {
                $location = self::REFERRER_BEFORE;
            }
            elseif (isset($upper[$referrer])) {
                $location = self::REFERRER_AFTER;
            }
        }

        $url = null;

        // If after then resolve the back URL and redirect
        if (self::REFERRER_AFTER == $location) {
            $url = $this->getBackUrl();
        }

        // If before then resolve the next URL and redirect
        if (self::REFERRER_BEFORE == $location) {
            $url = $this->getNextUrl();
        }

        if ($url) {
            // todo: replace this with a structured approach
            header("Location: {$url}\n");
            exit;
        }
    }

    /**
     * Parse referrer,
     *
     * @param string $referrer
     * @return bool|string
     */
    private function getReferrerPath($referrer)
    {
        $parts = parse_url($referrer);

        if (isset($parts['path'])) {
            return rtrim($parts['path'], '/');
        }

        return false;
    }

    /**
     * Get the URL path from request
     *
     * @return string
     */
    private function getRequestUrlPath()
    {
        return rtrim($this->request->getPathInfo(), '/');
    }

    /**
     * Initialise the form flow, I.e. add steps
     *
     * @return void
     */
    abstract public function initialise();

    /**
     * Set progressiveStore
     *
     * @param \Iris\ProgressiveStore\ProgressiveStoreInterface $progressiveStore
     * @return $this
     */
    public function setProgressiveStore(ProgressiveStoreInterface $progressiveStore)
    {
        $this->progressiveStore = $progressiveStore;
        return $this;
    }

    /**
     * Get progressiveStore
     *
     * @return \Iris\ProgressiveStore\ProgressiveStoreInterface
     */
    public function getProgressiveStore()
    {
        return $this->progressiveStore;
    }

    /**
     * Set form
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @return $this
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Get form
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set steps
     *
     * @param array $steps
     * @return $this
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Set request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get product from application held in the
     * progressive store
     *
     * @throws ProductNotFoundOnApplicationException
     * @return Product
     */
    public function getProductFromApplicationInStore()
    {
        /** @var ReferencingApplication $application */
        $application = $this
            ->progressiveStore
            ->getPrototypeByClass(
                'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication'
            )
        ;

        $product = $application->getProduct();
        if (!($product instanceof Product)) {
            throw new ProductNotFoundOnApplicationException(
                sprintf('Product not found on application with UUID %s', $application->getReferencingApplicationUuId())
            );
        }

        return $product;
    }

    /**
     * Set canAgentCompleteProspectiveLandlord
     *
     * @param string $canAgentCompleteProspectiveLandlord
     * @return $this
     */
    public function setCanAgentCompleteProspectiveLandlord($canAgentCompleteProspectiveLandlord)
    {
        $this->canAgentCompleteProspectiveLandlord = $canAgentCompleteProspectiveLandlord;
        return $this;
    }

    /**
     * Get canAgentCompleteProspectiveLandlord
     *
     * @return string
     */
    public function getCanAgentCompleteProspectiveLandlord()
    {
        return $this->canAgentCompleteProspectiveLandlord;
    }
}