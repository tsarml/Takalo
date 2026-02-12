<?php

class ObjectController {

    public function listAll() {
        // À remplacer par ta vraie requête plus tard
        $objects = [
            ['id'=>1, 'user_id'=>2, 'name'=>'iPhone 11', 'photo'=>'/img/iphone.jpg', 'condition'=>'Bon', 'category'=>'Téléphone', 'description'=>'Très bon état, coque offerte', 'estimated_value'=>1200000],
            ['id'=>2, 'user_id'=>1, 'name'=>'Veste en cuir', 'photo'=>null, 'condition'=>'Neuf', 'category'=>'Vêtement', 'description'=>'Taille M, jamais portée', 'estimated_value'=>85000],
            // ... (données de test)
        ];

        $current_user_id = 1; // ← simuler (à remplacer par $_SESSION['user_id'])

        Flight::render('objects/list', [
            'objects' => $objects,
            'current_user_id' => $current_user_id
        ]);
    }

    public function listMyObjects() {
        // Simulation – plus tard : WHERE user_id = current_user
        $my_objects = [
            ['id'=>2, 'name'=>'Veste en cuir', 'photo'=>null, 'estimated_value'=>85000],
            ['id'=>5, 'name'=>'Livre - Le Petit Prince', 'photo'=>'/img/petitprince.jpg', 'estimated_value'=>15000],
        ];

        // Pour l'instant on ignore le paramètre ?for_exchange=
        Flight::render('objects/my-objects', [
            'my_objects' => $my_objects
        ]);
    }
}