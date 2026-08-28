<?php

namespace Tests\Unit\TestUnit;

function calcularNumero(int $n1, int $n2){
    return n1 + n2;
}

test('Ejemplo', function () {
    $calculo = calcularNumero(1, 1);
    expect($calculo)->toBe(2);
});
