<?php
require_once "Mailer.php";

class Contact {
    private $recipient;

    public function __construct($recipient) {
        $this->recipient = $recipient;
    }

    /* Send contact message to appropriate mediator */
    public function relay_contact_message($contact_data) {
        if (isset($contact_data["name"]) && isset($contact_data["email"]) && isset($contact_data["message"])) {
            /* get contact form data */
            $name = $contact_data["name"];
            $email = $contact_data["email"];
            $message = $contact_data["message"];
            $phone = ($contact_data["phone-number"]) ? $contact_data["phone-number"] : "not specified";
            /* build message */
            $mail = "<b>Name: </b>".$name."<br><b>Email: </b>".$email."<br><b>Phone number: </b>"
                        .$phone."<br><hr><em>Message: </em><br>".$message;
            Mailer::mail($this->recipient, "[Contact] New message from contact form", $mail);
            return array("status" => "success");
        } else {
            return array("status" => "error");
        }
    }
}
?>