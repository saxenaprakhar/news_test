<?php
/**
 * Order Observer Model
 *
 * @category    Model
 * @package     Zest_Neworder
 * @author      Prakhar Saxena
 */

class Zest_NewOrder_Model_Orderobserver{
	/**
	* Function to send an email to admin when an order is placed
	*/
	public function orderSaved( $observer ) {
		$order = $observer->getEvent()->getOrder();
			
			if ( $order->getStatus() == "pending")
				$this->notifyAdmin( $order );
		}
	}
	
	/*
	 * Notifies Admin of an order
	 *
	 * @param $object
	 * @return void
	 */
	private function notifyAdmin( $order ) {
			
		$items = $order->getItemsCollection();
		foreach ( $items as $item ) {
			$product_id = $item->getProductId();
			$this->emailAdmin($order);
			}
		}
	
	/*
	 * Sends email to Admin
	 *
	*/
	private function emailAdmin( $order ) {
		//print_r($order);
		$email_html = '';
		
		
		// Traverse products and build HTML email
		foreach ( $products as $product_id => $qty ){
			$_product = Mage::getModel( 'catalog/product' )->load( $product_id );
			
			$email_html .= '<tr><td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA;">'. $_product->getName() .' ( '. $_product->getSku() .' )</td>';
			$email_html .= '<td align="center" valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">'. number_format( $qty, 2 ) .'</td></tr>';
		}
		
		// Load template and send email
		if ( $email_html != '' ) {
			// Get Admin Mail
			$to_email = Mage::getStoreConfig('trans_email/ident_general/value');
					
			$email_template = Mage::getModel( 'core/email_template' )->loadDefault( 'zest_neworder_email' );
			
			try {
				$email_content = $email_template->getProcessedTemplate( array( 'order' => $order, 'store' => Mage::app()->getStore(), 'items_html' => $email_html ) );
				$email_subject = $email_template->getProcessedTemplateSubject( array( 'order' => $order, 'store' => Mage::app()->getStore(), 'items_html' => $email_html ) );
				
				$mail = Mage::getModel( 'core/email' );
				$mail->setToName( 'Admin' );
				$mail->setToEmail( $to_email );
				$mail->setBody( $email_content );
				$mail->setSubject( $email_subject );
				$mail->setType( 'html' );
				$mail->setFromName('no-reply' );
				$mail->setFromEmail( 'no-reply@'echo Mage::app()->getStore()->getHomeUrl() );

				$mail->send();
			}
			catch( Exception $e ) {}
		}
	}
}
?>