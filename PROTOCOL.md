
Friend Info
-----------

| You                           | Potential Friend                                                          |
| ----------------------------- | ------------------------------------------------------------------------- |
| -> GET URL/info               |                                                                           |
|                               |                                                                           |
|                               | <- Reponse                                                                |
|                               | JSON data :                                                               |
|                               | - sitename: the website official name                                     |
|                               | - adminEmail: the email address of Herisson site administrator            |
|                               | - version: the version of Herisson protocol used by this site             |
|                               |                                                                           |
|                               |  Reponses code                                                            |
|                               | - 200 : OK                                                                |
|                               | - 404 : Not Found -> This site is not a Herisson site or is closed        |



Friend Key
----------

| You                           | Potential Friend                                                          |
| ----------------------------- | ------------------------------------------------------------------------- |
| -> GET URL/publicKey          |                                                                           |
|                               |                                                                           |
|                               |                                                                           |
|                               | <- Reponse                                                                |
|                               | Public key as plain text                                                  |
|                               |                                                                           |
|                               | Reponses code                                                             |
|                               | - 200 : OK                                                                |
|                               | - 404 : Not Found -> This site is not a Herisson site or is closed        |



Friend Request
--------------

| You                           | Potential Friend                                                          |
| ----------------------------- | ------------------------------------------------------------------------- |
| -> POST URL/ask               |                                                                           |
| POST data:                    |                                                                           |
| - url: URL                    |                                                                           |
| - signature: URL encrypted    |                                                                           |
|   with private key            |                                                                           |
|                               | <- Reponse                                                                |
|                               |                                                                           |
|                               | Reponses code                                                             |
|                               | - 200 : OK -> friend need to manually validate your request, your         |
|                               |         request is pending, you will be notified later if it is validated |
|                               | - 202 : Accepted -> friend automatically validate your request            |
|                               | - 403 : Forbidden -> This site refuses new friends                        |
|                               | - 404 : Not Found -> This site is not a Herisson site or is closed        |
|                               | - 417 : Expectation Failed -> Transmission protocol error,                |
|                               |         probably with public/private encryption                           |
 



Friend validation
-----------------

| You                           | Potential Friend                                                          |
| ----------------------------- | ------------------------------------------------------------------------- |
| -> POST URL/valide            |                                                                           |
| POST data:                    |                                                                           |
| - url: URL                    |                                                                           |
| - signature: URL encrypted    |                                                                           |
|   with private key            |                                                                           |
|                               | <- Reponse                                                                |
|                               | 1                                                                         |
|                               |                                                                           |
|                               | Reponses code                                                             |
|                               | - 200 : OK -> friend receive your validation                              |
|                               |         and the friendship is now mutual                                  |
|                               | - 404 : Not Found -> This site is not a Herisson site or is closed        |
|                               | - 417 : Expectation Failed -> Transmission protocol error,                |
|                               |         probably with public/private encryption                           |
 




Friend search
-------------

| You                           | Friend                                                                    |
| ----------------------------- | ------------------------------------------------------------------------- |
| -> POST URL/retrieve          |                                                                           |
| POST data:                    |                                                                           |
| - key: your public key        |                                                                           |
| Optionnal data                |                                                                           |
| - search : a keyword to       |                                                                           |
|   narrow the search           |                                                                           |
|                               |                                                                           |
|                               |                                                                           |
|                               | <- Reponse                                                                |
|                               | JSON data :                                                               |
|                               | - data : base64 encoded string of the AES encrypted data from the         |
|                               |          JSON bookmarks data, using the given IV                          |
|                               | - hash : base64 encoded string of the public key encrypted data           |
|                               |          so only you will be able to read it.                             |
|                               | - iv   : the initialization vector for decryption                         |
|                               |                                                                           |
|                               | Reponses code                                                             |
|                               | - 200 : OK -> friend sent his encrypted bookmarks data                    |
|                               | - 404 : Not Found -> This site is not a Herisson site or is closed        |
|                               | - 417 : Expectation Failed -> Transmission protocol error,                |
|                               |         probably with public/private encryption                           |
 

