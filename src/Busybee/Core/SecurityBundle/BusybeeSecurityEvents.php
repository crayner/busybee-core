<?php

namespace Busybee\Core\SecurityBundle;

/**
 * Contains all events thrown in the BusybeeSecurityBundle
 */
final class BusybeeSecurityEvents
{

	/**
	 * The REGISTRATION_COMPLETED event occurs after saving the user in the registration process.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterUserResponseEvent instance.
	 */
	const REGISTRATION_COMPLETED = 'busybee_security.registration.completed';

	/**
	 * The REGISTRATION_CONFIRMED event occurs after confirming the account.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterUserResponseEvent instance.
	 */
	const REGISTRATION_CONFIRMED = 'busybee_security.registration.confirmed';

	/**
	 * The REGISTRATION_INITIALISE event occurs when the registration process is initialized.
	 *
	 * This event allows you to modify the default values of the user before binding the form.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\UserEvent instance.
	 */
	const REGISTRATION_INITIALISE = 'busybee_security.registration.initialise';

	/**
	 * The REGISTRATION_SUCCESS event occurs when the registration form is submitted successfully.
	 *
	 * This event allows you to set the response instead of using the default one.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FormEvent instance.
	 */
	const REGISTRATION_SUCCESS = 'busybee_security.registration.success';

	/**
	 * The RESETTING_RESET_INITIALISE event occurs when the resetting process is initialized.
	 *
	 * This event allows you to set the response to bypass the processing.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\GetResponseUserEvent instance.
	 */
	const RESETTING_RESET_INITIALISE = 'busybee_security.resetting.reset.initialise';

	/**
	 * The RESETTING_RESET_COMPLETED event occurs after saving the user in the resetting process.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterUserResponseEvent instance.
	 */
	const RESETTING_RESET_COMPLETED = 'busybee_security.resetting.reset.completed';

	/**
	 * The RESETTING_RESET_SUCCESS event occurs when the resetting form is submitted successfully.
	 *
	 * This event allows you to set the response instead of using the default one.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FormEvent instance.
	 */
	const RESETTING_RESET_SUCCESS = 'busybee_security.resetting.reset.success';

	/**
	 * The ROLE_EDIT_INITIALISE event occurs when the group editing process is initialized.
	 *
	 * This event allows you to modify the default values of the user before binding the form.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\GetResponseGroupEvent instance.
	 */
	const ROLE_EDIT_INITIALISE = 'busybee_security.role.edit.initialise';

	/**
	 * The ROLE_EDIT_SUCCESS event occurs when the group edit form is submitted successfully.
	 *
	 * This event allows you to set the response instead of using the default one.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FormEvent instance.
	 */
	const ROLE_EDIT_SUCCESS = 'busybee_security.role.edit.success';

	/**
	 * The ROLE_EDIT_COMPLETED event occurs after saving the group in the group edit process.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterGroupResponseEvent instance.
	 */
	const ROLE_NEW_COMPLETED = 'busybee_security.role.edit.completed';

	/**
	 * The ROLE_NEW_INITIALISE event occurs when the group editing process is initialized.
	 *
	 * This event allows you to modify the default values of the user before binding the form.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\GetResponseRoleEvent instance.
	 */
	const ROLE_NEW_INITIALISE = 'busybee_security.role.new.initialise';

	/**
	 * The ROLE_EDIT_SUCCESS event occurs when the group edit form is submitted successfully.
	 *
	 * This event allows you to set the response instead of using the default one.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FormEvent instance.
	 */
	const ROLE_NEW_SUCCESS = 'busybee_security.role.edit.success';

	/**
	 * The ROLE_EDIT_COMPLETED event occurs after saving the group in the group edit process.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterGroupResponseEvent instance.
	 */
	const ROLE_EDIT_COMPLETED = 'busybee_security.role.edit.completed';

	/**
	 * The SECURITY_IMPLICIT_LOGIN event occurs when the user is logged in programmatically.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\UserEvent instance.
	 */
	const SECURITY_IMPLICIT_LOGIN = 'busybee_security.implicit_login';

	/**
	 * The USER_EDIT_INITIALISE event occurs when the USER editing process is initialized.
	 *
	 * This event allows you to modify the default values of the user before binding the form.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\GetResponseUserEvent instance.
	 */
	const USER_EDIT_INITIALISE = 'busybee_security.user.edit.initialize';

	/**
	 * The USER_EDIT_SUCCESS event occurs when the USER edit form is submitted successfully.
	 *
	 * This event allows you to set the response instead of using the default one.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FormEvent instance.
	 */
	const USER_EDIT_SUCCESS = 'busybee_security.user.edit.success';

	/**
	 * The USER_EDIT_COMPLETED event occurs after saving the user in the USER edit process.
	 *
	 * This event allows you to access the response which will be sent.
	 * The event listener method receives a Busybee\Core\SecurityBundle\Event\FilterUserResponseEvent instance.
	 */
	const USER_EDIT_COMPLETED = 'busybee_security.user.edit.completed';

}