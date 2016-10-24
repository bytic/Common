## Notify Action
Orice action este insotit de un cod de eroare si de un mesaj de eroare. 
Acestea pot fi citite folosind $cod_eroare = $objPmReq->objPmNotify->errorCode;
respectiv $mesaj_eroare = $objPmReq->objPmNotify->errorMessage;
pentru a identifica ID-ul comenzii pentru care primim rezultatul platii folosim $id_comanda = $objPmReq->orderId;


#### confirmed
cand action este confirmed avem certitudinea ca banii au plecat din contul posesorului de card si facem update al starii comenzii si livrarea produsului

#### confirmed_pending
cand action este confirmed_pending inseamna ca tranzactia este in curs de verificare antifrauda.
Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare pentru o actiune de confirmare sau anulare.

#### paid_pending
cand action este paid_pending inseamna ca tranzactia este in curs de verificare.
Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare
pentru o actiune de confirmare sau anulare.

#### paid
cand action este paid inseamna ca tranzactia este in curs de procesare.
Nu facem livrare/expediere. In urma trecerii de aceasta procesare se va primi o noua
notificare pentru o actiune de confirmare sau anulare.

#### canceled
cand action este canceled inseamna ca tranzactia este anulata. Nu facem livrare/expediere.

#### credit
cand action este credit inseamna ca banii sunt returnati posesorului de card.
Daca s-a facut deja livrare, aceasta trebuie oprita sau facut un reverse.

