<?php
class Validator {
  public static function validateGuestOrder($data) {
      if (empty($data['guest']['name']) || empty($data['guest']['email'])) {
          return ['valid' => false, 'message' => 'Nom et email sont obligatoires'];
      }
      if (!filter_var($data['guest']['email'], FILTER_VALIDATE_EMAIL)) {
          return ['valid' => false, 'message' => 'Email invalide'];
      }
      if (empty($data['items']) || !is_array($data['items'])) {
          return ['valid' => false, 'message' => 'Le panier est vide ou incorrect'];
      }
      return ['valid' => true];
  }
}
