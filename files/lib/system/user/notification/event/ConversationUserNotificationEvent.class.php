<?php
namespace wcf\system\user\notification\event;
use wcf\data\user\notification\UserNotificationEditor;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * User notification event for conversations.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2014 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.conversation
 * @subpackage	system.user.notification.event
 * @category	Community Framework
 */
class ConversationUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.conversation.title');
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.conversation.message', array(
			'conversation' => $this->userNotificationObject,
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.conversation.mail', array(
			'conversation' => $this->userNotificationObject,
			'author' => $this->author,
			'notificationType' => $notificationType
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('Conversation', array('object' => $this->userNotificationObject));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::checkAccess()
	 */
	public function checkAccess() {
		if (!$this->userNotificationObject->canRead()) {
			// remove notification
			$userNotificationEditor = new UserNotificationEditor($this->notification);
			$userNotificationEditor->delete();
			
			// reset user storage
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'userNotificationCount');
			
			return false;
		}
		
		return true;
	}
}
