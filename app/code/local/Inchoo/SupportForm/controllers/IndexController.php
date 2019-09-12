<?php
 
class Inchoo_SupportForm_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();   
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'inchoo.support_form',
            array(
                'template' => 'inchoo/support_form.phtml'
            )
        );
 		$this->getLayout()->getBlock('head')->setTitle($this->__('Support Form Online'));
        $this->getLayout()->getBlock('content')->append($block);
		$template = Mage::getConfig()->getNode('global/page/layouts/one_column/template');
		$this->getLayout()->getBlock('root')->setTemplate($template);
        //$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
 
        $this->_initLayoutMessages('core/session');
 
        $this->renderLayout();
    }
 
    public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();
 
        $mail = new Zend_Mail();
		//$admin_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
		$admin_email = 'example@abc.com';
		$admin_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
		$bodytext = '
            Name: ' . $params['firstname'] . ' '.$params['lastname'].'
            Type Of Permit Or Certificate: ' . $params['type_of_permit'] . '
            Address Line 2: ' . $params['address2'] . '
            Birthdate: ' . $params['birthdate'] . '
            State: ' . $params['state'] . '         
            Contact Email: ' . $params['email'] . '
            Phone #: ' . $params['phone_number'] . '   
			Preferable method of contact: ' . $params['preferrred_contact'] . '           
            Notes:
            ' . $params['SupportTextArea'];
        $mail->setBodyText($bodytext);
        $mail->setFrom($params['email'], $params['firstname']);
        $mail->addTo($admin_email, $admin_name);
        $mail->setSubject('Example website support email');
        try {
            $mail->send();
            Mage::getSingleton('core/session')->addSuccess('Thanks for submitting.We will contact you soon!');
        }        
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Unable to send email. support email notification error.');
 
        }
 
       
 	
        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('supportform/');
    }
	public function recaptchaAction()
    {
		require_once(Mage::getBaseDir().DS.'lib'.DS.'reCaptcha/recaptchalib.php');
		$publickey = ""; // google recaptcha public key ,you got this from the signup page
		$privatekey = ""; //google recaptcha private key
		
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
		
		if ($resp->is_valid) {
			?>success<?
		}
		else 
		{
			die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
			   "(reCAPTCHA said: " . $resp->error . ")");
		}
	}
}
 
?>