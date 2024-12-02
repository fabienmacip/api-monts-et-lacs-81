## Explication des tables

- **`users`** : Contient les informations des utilisateurs enregistrés, incluant leur email, mot de passe (qui devrait être haché), nom, téléphone, etc.
- **`products`** : Contient les informations sur les produits disponibles à la vente (nom, description, prix, stock).
- **`orders`** : Table principale pour les commandes. Elle contient des références à l'utilisateur (si connecté) ou des informations d'invité (nom, email, téléphone). Le total et le statut de la commande sont également stockés.
- **`order_items`** : Détaille les produits d'une commande (un produit peut être ajouté plusieurs fois à une commande). Elle contient la quantité et le prix du produit au moment de la commande.
- **`sessions`** (optionnel) : Si vous implémentez une gestion de session ou de jetons JWT pour l'authentification, cette table est utile. Elle garde la trace des sessions des utilisateurs.
