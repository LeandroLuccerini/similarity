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
            ['ciao mondo', 'ciao mondo'],

            // maiuscole → minuscole
            ['CIAO MONDO', 'ciao mondo'],

            // spazi da tagliare
            ['   ciao mondo   ', 'ciao mondo'],

            // spazi multipli ridotti a uno
            ['ciao    mondo   bello', 'ciao mondo bello'],

            // punteggiatura rimossa
            ['ciao, mondo! bello?', 'ciao mondo bello'],

            // accenti e translitterazione
            ['città naïve élève über', 'citta naive eleve uber'],

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
            ['你好世界', 'ni hao shi jie'], // potresti ottenere 'nihaoshijie' a seconda di ICU locale

            // emoji e simboli non ASCII
            ['ciao 🌍!', 'ciao'], // rimuove tutto tranne caratteri alfanumerici e spazi

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