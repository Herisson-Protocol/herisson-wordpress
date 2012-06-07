<?php

function herisson_generate_keys_pair() {

 // Create the keypair
 $res=openssl_pkey_new();

 // Get private key
 openssl_pkey_export($res, $privkey);

 // Get public key
 $pubkey=openssl_pkey_get_details($res);
 $pubkey=$pubkey["key"];

 #print "public : $pubkey\n";
 #print "private : $privkey\n";

 return array($pubkey,$privkey);
}


function herisson_cipher($data) {
 list($pub1,$priv1) = herisson_generate_keys_pair();
 list($pub2,$priv2) = herisson_generate_keys_pair();

# $options = get_option('HerissonOptions');
 $sealed = null;

 $data = "Only I know the purple fox. Trala la !";
 $crypted0 = $data;
 
 echo openssl_public_encrypt($crypted0,$crypted1,$pub2)."\n";
 print_r("crypted1 : $crypted1\n");
 
 echo openssl_private_encrypt($crypted1,$crypted2,$priv1)."\n";
 print_r("crypted2 : $crypted2\n");
 
 echo openssl_public_decrypt($crypted2,$crypted3,$pub1)."\n";
 print_r("crypted3 : $crypted3\n");
 
 echo openssl_private_decrypt($crypted3,$crypted4,$priv2)."\n";
 print_r("crypted4 : $crypted4\n");

}


herisson_cipher('toto');
