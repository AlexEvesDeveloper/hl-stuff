<?php

/**
 * Represents a letting agent user in the system.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_User extends Model_Abstract {

    /**
     * The agent user's name.
     *
     * @var string
     */
	public $name;

    /**
     * The agent user's unique ID.
     *
     * @var int
     */
	public $id;

    /**
     * The agent user's agency's unique identifier.
     *
     * @var mixed Value may be an integer or a string.
     */
	public $agentSchemeNumber;

    /**
     * The agent user's username.
     *
     * @var string
     */
	public $username;

    /**
     * The agent user's password.
     *
     * @var string Password that should be one-way encrypted, but legacy
     * passwords are stored in plaintext.
     */
	public $password;

    /**
     * The agent user's password encryption scheme.
     *
     * @var string Indicates the encryption scheme to use when comparing a
     * given plaintext password (eg, supplied during login) with the stored
     * password.  Typical values are '' (none), 'md5', 'sha1', etc.
     */
	public $passwordEncryptionScheme;

    /**
     * The agent user's password encryption scheme's salt value, if any.
     *
     * @var string Indicates the encryption scheme salt to use when comparing a
     * given plaintext password (eg, supplied during login) with the stored
     * password.  Some schemes have no salt.
     */
    public $passwordEncryptionSalt;

    /**
     * The agent user's e-mail address.
     *
     * @var Model_Core_EmailAddress
     */
	public $email;

	/**
     * The agent user's secondary/supervisor's e-mail address.
     *
     * @var Model_Core_EmailAddress
     */
    public $copyMailTo;

    /**
     * The agent user's system status.
     *
     * @var string Must be a value corresponding to constants in Model_Core_Agent_UserStatus.
     */
	public $status;

    /**
     * The agent user's role.
     *
     * @var string Must be a value corresponding to constants in Model_Core_Agent_UserRole.
     */
	public $role;

    /**
     * The agent user's available resources.
     *
     * @var array Array of values corresponding to constants in Model_Core_Agent_UserResources.
     */
	public $resources;

    /**
     * The agent user's last login date.
     *
     * @var string
     */
	public $lastLoginDate;

    /**
     * The agent user's external news preference.  In practice the value of
     * this setting is overriden by that of any associated
     * Model_Core_Agent::enableExternalNews, if
     * Model_Core_Agent::enableExternalNews is set to false.
     *
     * @var bool Indicates if an agent user wants to see external news.
     */
	public $enableExternalNews;

    /**
     * The agent user's news category preferences.
     *
     * @var array Array of Model_Cms_ExternalNews_Category indicating the news
     * categories a user wants to see.
     */
	public $newsCategoryPreferences;

    /**
     * The agent user's security question ID.
     *
     * @var int
     */
	public $securityQuestionId;

    /**
     * The agent user's security answer.
     *
     * @var string
     */
	public $securityAnswer;
}