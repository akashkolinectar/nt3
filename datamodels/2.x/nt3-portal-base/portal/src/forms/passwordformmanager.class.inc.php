<?php

namespace Combodo\nt3\Portal\Form;

use Exception;
use Dict;
use UserRights;
use Combodo\nt3\Form\FormManager;
use Combodo\nt3\Form\Form;
use Combodo\nt3\Form\Field\HiddenField;
use Combodo\nt3\Form\Field\PasswordField;

/**
 * Description of passwordformmanager
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class PasswordFormManager extends FormManager
{
	const FORM_TYPE = 'change_password';

	public function Build()
	{
		// Building the form
		$oForm = new Form('change_password');

		// Adding hidden field with form type
		$oField = new HiddenField('form_type');
		$oField->SetCurrentValue('change_password');
		$oForm->AddField($oField);

		// Adding old password field
		$oField = new PasswordField('old_password');
		$oField->SetMandatory(true)
			->SetLabel(Dict::S('UI:Login:OldPasswordPrompt'));
		$oForm->AddField($oField);
		// Adding new password field
		$oField = new PasswordField('new_password');
		$oField->SetMandatory(true)
			->SetLabel(Dict::S('Brick:Portal:UserProfile:Password:ChoosePassword'));
		$oForm->AddField($oField);
		// Adding confirm password field
		$oField = new PasswordField('confirm_password');
		$oField->SetMandatory(true)
			->SetLabel(Dict::S('Brick:Portal:UserProfile:Password:ConfirmPassword'));
		$oForm->AddField($oField);

		$oForm->Finalize();
		$this->oForm = $oForm;
		$this->oRenderer->SetForm($this->oForm);
	}

	/**
	 * Validates the form and returns an array with the validation status and the messages.
	 * If the form is valid, creates/updates the object.
	 *
	 * eg :
	 *  array(
	 * 	  'status' => true|false
	 * 	  'messages' => array(
	 * 		  'errors' => array()
	 * 	)
	 *
	 * @param array $aArgs
	 * @return array
	 */
	public function OnSubmit($aArgs = null)
	{
		$aData = array(
			'valid' => true,
			'messages' => array(
				'success' => array(),
				'warnings' => array(), // Not used as of today, just to show that the structure is ready for change like this.
				'error' => array()
			)
		);

		// Update object and form
		$this->OnUpdate($aArgs);

		// Check if form valid
		if ($this->oForm->Validate())
		{
			// The try catch is essentially to start a MySQL transaction
			try
			{
				// Updating password
				$sAuthUser = $_SESSION['auth_user'];
				$sOldPassword = $this->oForm->GetField('old_password')->GetCurrentValue();
				$sNewPassword = $this->oForm->GetField('new_password')->GetCurrentValue();
				$sConfirmPassword = $this->oForm->GetField('confirm_password')->GetCurrentValue();
				
				if ($sOldPassword !== '' && $sNewPassword !== '' && $sConfirmPassword !== '')
				{
					if (!UserRights::CanChangePassword())
					{
						$aData['valid'] = false;
						$aData['messages']['error'] += array('_main' => array(Dict::Format('Brick:Portal:UserProfile:Password:CantChangeContactAdministrator', nt3_APPLICATION_SHORT)));
					}
					else if (!UserRights::CheckCredentials($sAuthUser, $sOldPassword))
					{
						$aData['valid'] = false;
						$aData['messages']['error'] += array('old_password' => array(Dict::S('UI:Login:IncorrectOldPassword')));
					}
					else if ($sNewPassword !== $sConfirmPassword)
					{
						$aData['valid'] = false;
						$aData['messages']['error'] += array('confirm_password' => array(Dict::S('UI:Login:RetypePwdDoesNotMatch')));
					}
					else if (!UserRights::ChangePassword($sOldPassword, $sNewPassword))
					{
						$aData['valid'] = false;
						$aData['messages']['error'] += array('confirm_password' => array(Dict::Format('Brick:Portal:UserProfile:Password:CantChangeForUnknownReason', nt3_APPLICATION_SHORT)));
					}
					else
					{
						$aData['messages']['success'] += array('_main' => array(Dict::S('Brick:Portal:Object:Form:Message:Saved')));
					}
				}
			}
			catch (Exception $e)
			{
				$aData['valid'] = false;
				$aData['messages']['error'] += array('_main' => array($e->getMessage()));
				IssueLog::Error(__METHOD__ . ' at line ' . __LINE__ . ' : Exception during submit (' . $e->getMessage() . ')');
			}
		}
		else
		{
			// Handle errors
			$aData['valid'] = false;
			$aData['messages']['error'] += $this->oForm->GetErrorMessages();
		}
		
		return $aData;
	}

	public function OnUpdate($aArgs = null)
	{

		// We build the form
		$this->Build();

		// Then we update it with new values
		if (is_array($aArgs))
		{
			if (isset($aArgs['currentValues']))
			{
				foreach ($aArgs['currentValues'] as $sPreferenceName => $value)
				{
					$this->oForm->GetField($sPreferenceName)->SetCurrentValue($value);
				}
			}
		}
	}

	public function OnCancel($aArgs = null)
	{
		
	}

}
