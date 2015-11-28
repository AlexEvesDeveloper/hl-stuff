<?php
namespace RRP\Underwriting\Decorators;

use RRP\Underwriting\AbstractUnderwritingDecorator;
use RRP\Underwriting\UnderwritingDecoratorInterface;

/**
 * Class AddUnderwriting
 *
 * @package RRP\Underwriting\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusAnswers extends AbstractUnderwritingDecorator implements UnderwritingDecoratorInterface
{
    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Will this policy be a continuation of cover from a previous policy?'
     */
    const QUESTION_ID_CONTINUATION = 1;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Has the first month's rent been paid in advance?'
     */
    const QUESTION_ID_RENT_IN_ADVANCE = 2;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Are you aware of any circumstances which may give rise to a claim?'
     */
    const QUESTION_ID_CLAIM_CIRCUMSTANCES = 3;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Will only permitted occupiers be living in the property?'
     */
    const QUESTION_ID_PERMITTED_OCCUPIERS = 4;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Any tenancy disputes, including late payment of rent or rental arrears?'
     */
    const QUESTION_ID_TENANCY_DISPUTES = 5;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Is the property let on a written Assured Shorthold Tenancy(in England and Wales or the equivalent in Scotland
     *  and Northern Ireland)?'
     */
    const QUESTION_ID_TENANCY_AST = 6;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Have there been any claims logged during the existing tenancy?'
     */
    const QUESTION_ID_PRIOR_CLAIMS = 7;

    /**
     * underwritingQuestionID value in the underwritingQuestions table for the question
     * 'Was a deposit with a sum equivalent to (or greater than) 1 months rent taken prior to the commencement of the tenancy?'
     */
    const QUESTION_ID_DEPOSIT_SUFFICIENT = 8;

    /**
     * @inheritdoc
     */
    public function getQuestionList()
    {
        return array(
            self::QUESTION_ID_CONTINUATION,
            self::QUESTION_ID_RENT_IN_ADVANCE,
            self::QUESTION_ID_CLAIM_CIRCUMSTANCES,
            self::QUESTION_ID_PERMITTED_OCCUPIERS,
            self::QUESTION_ID_TENANCY_DISPUTES,
            self::QUESTION_ID_TENANCY_AST,
            self::QUESTION_ID_PRIOR_CLAIMS,
            self::QUESTION_ID_DEPOSIT_SUFFICIENT
        );
    }

}