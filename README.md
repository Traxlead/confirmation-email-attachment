Confirmation Email Attachment
=============================
Ce module permet d'inclure une pièce jointe supplémentaire dans l'e-mail de
confirmation de commande (comme des Conditions Générales de Vente par exemple).

## Installation ##
Pour installer ce module, il suffit de le placer dans le dossier modules de
votre PrestaShop

Dans votre interface d'administration : cliquez sur le menu "Modules" et 
recherchez "Confirmation de commande".

## Compatibilité ##
Ce module inclut un override de la classe PaymentModule. Il est donc conseillé
de vérifier qu'il n'interfère pas avec un autre module qui redéfinit la méthode
validateOrder();

Le module n'a été testé que sur la version 1.6.0.6 de PrestaShop.