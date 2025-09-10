<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Traits\apiresponse;

class ContactsController extends Controller
{
    use apiresponse;

    public function store(Request $request){

       $contact = new Contact;
        $contact->name = $request->name;
        $contact->country = $request->country;
        $contact->city = $request->city;
        $contact->post = $request->post;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->announce = $request->announce;
        $contact->message = $request->message;
        $contact->save();

        return $this->success($contact, 'Contact created successfully', 200);
    }
    
}
