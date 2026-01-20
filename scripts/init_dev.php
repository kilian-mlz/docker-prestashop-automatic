#!/usr/bin/env php
<?php
/**
 * Script de post-installation pour nettoyer la boutique et créer un client de test.
 */

// On attend que PrestaShop soit chargé
if (!defined('_PS_VERSION_')) {
    require_once '/var/www/html/config/config.inc.php';
}

echo "\n--- [INIT DEV] Démarrage du nettoyage ---\n";

// 1. SUPPRIMER LES PRODUITS
$products = Product::getProducts(Context::getContext()->language->id, 0, 0, 'id_product', 'ASC');
if ($products) {
    foreach ($products as $p) {
        $product = new Product($p['id_product']);
        if ($product->delete()) {
            echo "Produit supprimé : " . $p['name'] . "\n";
        }
    }
} else {
    echo "Aucun produit à supprimer.\n";
}

// 2. SUPPRIMER LES COMMANDES (S'il y en a)
$orders = Order::getOrdersWithInformations(null, null);
if ($orders) {
    foreach ($orders as $o) {
        $order = new Order($o['id_order']);
        $order->delete();
        echo "Commande supprimée ID: " . $o['id_order'] . "\n";
    }
}

// 3. CRÉER UN CLIENT DE TEST
$email = 'client@test.com';
$password = 'password123'; // Mot de passe simple pour le dev

if (!Customer::customerExists($email)) {
    $customer = new Customer();
    $customer->email = $email;
    $customer->passwd = Tools::encrypt($password); // Hachage compatible PS
    $customer->firstname = 'Jean';
    $customer->lastname = 'Test';
    $customer->active = 1;
    $customer->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');

    if ($customer->add()) {
        echo "\n--- [INIT DEV] Client créé avec succès ! ---\n";
        echo "Email : $email \n";
        echo "Pass  : $password \n";
    } else {
        echo "\n--- [ERREUR] Impossible de créer le client ---\n";
    }
} else {
    echo "\n--- [INIT DEV] Le client existe déjà ---\n";
}

echo "--- [INIT DEV] Terminé ---\n";

echo "--- [FIX] Rétablissement des droits sur le dossier var/ ---\n";
shell_exec('chown -R www-data:www-data /var/www/html/var');