<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\StringNormalizer;

class StringNormalizerTest extends TestCase
{
    public static function normalizeDataProvider(): array
    {
        return [
            // semplice minuscolo
            ['ciao mondo', 'ciaomondo'],

            // maiuscole → minuscole
            ['CIAO MONDO', 'ciaomondo'],

            // spazi da tagliare
            ['   ciao mondo   ', 'ciaomondo'],

            // spazi multipli ridotti a uno
            ['ciao    mondo   bello', 'ciaomondobello'],

            // punteggiatura rimossa
            ['ciao, mondo! bello?', 'ciaomondobello'],

            // accenti e translitterazione
            ['città naïve élève über', 'cittanaiveeleveuber'],

            // unicode combinato (é come e + accent combining)
            ["e\u{0301}cole", 'ecole'],

            // caratteri speciali vari
            ['@hello#world$123', 'helloworld123'],

            // solo spazi → null
            ['     ', null],

            // stringa vuota → null
            ['', null],

            // mix complesso di accenti e simboli
            ["  Héllò---Wörld!!  ", 'helloworld'],

            // solo numeri, restano invariati
            ['12345', '12345'],

            // numeri e lettere mescolati
            ['ABC123xyz', 'abc123xyz'],

            // solo simboli → null
            ['!@#$%^&*()', null],

            // caratteri non latini (cinese) – translitterati o rimossi
            ['你好世界', 'nihaoshijie'],

            // emoji e simboli non ASCII
            ['ciao 🌍!', 'ciao'],

            // caratteri accentati misti con numeri
            ['Café123', 'cafe123'],

            // testo con tab e newline
            ["ciao\tmondo\nbello", 'ciaomondobello'],
        ];
    }

    #[DataProvider("normalizeDataProvider")]
    public function testNormalizer(string $input, ?string $output): void
    {
        $n = new StringNormalizer();
        $this->assertEquals($output, $n->normalize($input));
    }
}