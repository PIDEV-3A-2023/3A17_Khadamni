<?php
namespace App\Service;

use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeService
{

    private $stripe;

public function __construct()
{
    $this->stripe = new \Stripe\StripeClient($_ENV['STRIPE_API_KEY']);

}


 public function finishSignUp($acc,$return_url) {
    try {
        $acclink= $this->stripe->accountLinks->create([
            'account' => $acc->id,
            'refresh_url' => 'http://127.0.0.1:8000/formation' , // URL to redirect to if the user refreshes the page
            'return_url' => $return_url, // URL to redirect to after the account setup is complete
            'type' => 'account_onboarding',
        ]);
        return $acclink;
    }
    catch (ApiErrorException $e) {
        throw new \Exception($e->getMessage());
    }

    return null;
 }

 public function retriveAccount($email) {
     $accounts = $this->stripe->accounts->all([]);

     do {
         $data = $accounts->data;
         foreach ($data as $acc) {
             if ($acc->email == $email)
                 return $acc;
             $new = $acc;
         }
         $accounts =  $this->stripe->accounts->all(['starting_after' => $new]);
     }while($accounts->has_more and count($accounts));
     return $this->stripe->accounts->create([
         'type'=> 'express',
         'email'=> $email
     ]);

 }
 public function retrieveProduct($formation) {
     $products = $this->stripe->products->all([]);
     $product = null;
     do {
         $data = $products->data;
         foreach ($data as $prd) {
             if ($prd->id == $formation->getIdFormation())
                 return $prd;
             $new = $prd;
         }
         $products = $this->stripe->products->all(['starting_after'=>$new]);
     }while($products->has_more and count($products));
     if (!isset($product)) {
         $product = $this->stripe->products->create([
             'id' => $formation->getIdFormation(),
             'name' => $formation->getNomFormation(),
             'description' => $formation->getDescription(),
             'default_price_data' => [
                 'currency' => 'usd',
                 'unit_amount' => $formation->getPrix()*100
             ]
         ]);
     }
     return $product;
 }
    public function retrieveCustomer($user) {
        $customers = $this->stripe->customers->all([]);
        $customer = null;
        do {
            $data = $customers->data;
            foreach ($data as $cst) {
                if ($cst->email == $user->getEmail())
                    return $cst;
                $new = $cst;
            }
            $customers = $this->stripe->customers->all(['starting_after'=>$new]);
        }while($customers->has_more and count($customers));
        if (!isset($customer)) {
            $customer =$this->stripe->customers->create([
                'email' => $user->getEmail(),
                'name' => $user->getNom() . " " .$user->getPrenom(),

            ]);}
        return $customer;
    }

 public function CreateCheckoutSession($user,$formation,$email,$success_url) {
    $product = $this->retrieveProduct($formation);
    $acc = $this->retriveAccount($email);
    $customer = $this->retrieveCustomer($user);
    $prix = $this->stripe->prices->retrieve($product->default_price,[]);


     $checkout = $this->stripe->checkout->sessions->create([
         'mode' => 'payment',
         'line_items' => [['price' => $prix->id, 'quantity' => 1]],
         'success_url' => $success_url,
         'cancel_url' => 'http://127.0.0.1:8000/formation',
         'payment_intent_data' => [
             'metadata' => ['source'=>$user->getIdUser().$formation->getIdFormation()],
             'application_fee_amount' => $prix->unit_amount * 0.1,
             'transfer_data' => [
                 'destination' => $acc->id

             ],
             'setup_future_usage' => 'on_session',
         ],
         'phone_number_collection' => ['enabled' => false],
         'customer' => $customer->id,
         'payment_method_types' => ['card'],
         'locale' => 'fr',

     ]);
    return $checkout;

 }
 public function refundMoney($user,$formation,$formateur,$amount) {
     $customer = $this->retrieveCustomer($user);
     $receiver = $this->retriveAccount($formateur->getEmail());

     $key = $user->getIdUser().$formation->getIdFormation();

     $query = "status:'succeeded' AND metadata['source']:'" . $key . "' AND customer:'" . $customer->id . "'";



     $p = $this->stripe->paymentIntents->search(['query' => $query,'limit' => '1'
     ]);
     $t = $this->stripe->transfers->all(['destination' => $receiver->id , 'transfer_group' => $p->data[0]->transfer_group
     ]);

     return $this->stripe->transfers->createReversal(
         $t->data[0]->id,
         ['amount' => $amount , 'refund_application_fee' => true]
     );

 }

 public function testStripe ($id) {

       $tr= $this->stripe->transfers->retrieveReversal($id);
        dd($tr);



     }

}
