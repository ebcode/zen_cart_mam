<?php
// require the autoloader
//error_reporting(E_ALL);
//ini_set('display_errors',1);

//echo "<br>4:  requiring autoloader";

require_once 'Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

// Enter your Constant Contact APIKEY and ACCESS_TOKEN
define("APIKEY", "3kj8ntfufusea8gumb27qkwk");
define("ACCESS_TOKEN", "51054b06-bb2a-48f0-ab0e-130e384c51a0");

$cc = new ConstantContact(APIKEY);

//echo "<br>20: new cc okay";

try {
        // check to see if a contact with the email addess already exists in the account
        //echo "<br>24: calling CC API: getContactByEmail w/ email: $email_address";
        $response = $cc->getContactByEmail(ACCESS_TOKEN, $email_address);

        // create a new contact if one does not exist
        if (empty($response->results)) {
            
            $action = "Creating Contact";           
            $contact = new Contact();
            $contact->addEmail($email_address);
            $contact->addList('2');
            $contact->first_name = $firstname;
            $contact->last_name = $lastname;

            /*
             * The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
             * Contact that this action is being performed by the contact themselves, and gives the ability to
             * opt contacts back in and trigger Welcome/Change-of-interest emails.
             *
             * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
             */
            
            //echo "<br> calling addContact here";
            
            $returnContact = $cc->addContact(ACCESS_TOKEN, $contact, false);

            // update the existing contact if address already existed
        } else {
            $action = "Updating Contact";            
            //echo "<br> would call updateContact here";            
            $contact = $response->results[0];
            $contact->addList('2');
            $contact->first_name = $firstname;
            $contact->last_name = $lastname;

            /*
             * The third parameter of updateContact defaults to false, but if this were set to true it would tell
             * Constant Contact that this action is being performed by the contact themselves, and gives the ability to
             * opt contacts back in and trigger Welcome/Change-of-interest emails.
             *
             * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
             */
            $returnContact = $cc->updateContact(ACCESS_TOKEN, $contact, false);
        }

        // catch any exceptions thrown during the process and print the errors to screen
    } catch (CtctException $ex) {
        echo '<span class="label label-important">Error ' . $action . '</span>';
        echo '<div class="container alert-error"><pre class="failure-pre">';
        print_r($ex->getErrors());
        echo '</pre></div>';
        die();
    }
    
    //echo "<br> 83: constant contact end";

    //die('OK');
