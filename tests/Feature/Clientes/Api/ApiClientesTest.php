<?php

use App\Models\User;


// Comprueba simplemente si el usuario esta autenticado para realizar esta peticion nada mas
test('the_user_needs_to_be_authenticated', function () {
    $response = $this->get('/api/clientes');
    $response->assertStatus(401);
});

// Comprueba que un usuario autenticado no pueda hacer la peticion si no tiene un negocio vinculado a su cuenta
test('the_user_needs_have_a_bussines', function() {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/api/clientes?buscar=Elias');
    $response->assertStatus(422)
        ->assertJson(['error' => 'No tienes un negocio vinculado a tu cuenta para realizar esta petición.']);
});

