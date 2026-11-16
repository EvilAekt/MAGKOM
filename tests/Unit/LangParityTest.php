<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LangParityTest extends TestCase
{
    /**
     * Memastikan id.json dan en.json ada dan merupakan JSON yang valid.
     */
    public function test_lang_files_exist_and_are_valid_json(): void
    {
        $idPath = __DIR__ . '/../../lang/id.json';
        $enPath = __DIR__ . '/../../lang/en.json';

        $this->assertFileExists($idPath);
        $this->assertFileExists($enPath);

        $idContent = file_get_contents($idPath);
        $enContent = file_get_contents($enPath);

        $idData = json_decode($idContent, true);
        $enData = json_decode($enContent, true);

        $this->assertIsArray($idData, 'lang/id.json harus berisi JSON object valid');
        $this->assertIsArray($enData, 'lang/en.json harus berisi JSON object valid');
    }

    /**
     * Memastikan himpunan key id.json dan en.json identik 1:1.
     */
    public function test_lang_keys_parity_is_identical(): void
    {
        $idPath = __DIR__ . '/../../lang/id.json';
        $enPath = __DIR__ . '/../../lang/en.json';

        $idData = json_decode(file_get_contents($idPath), true);
        $enData = json_decode(file_get_contents($enPath), true);

        $missingInId = array_diff_key($enData, $idData);
        $missingInEn = array_diff_key($idData, $enData);

        $this->assertEmpty(
            $missingInId,
            'Key berikut ada di en.json tetapi tidak ada di id.json: ' . implode(', ', array_keys($missingInId))
        );

        $this->assertEmpty(
            $missingInEn,
            'Key berikut ada di id.json tetapi tidak ada di en.json: ' . implode(', ', array_keys($missingInEn))
        );

        $this->assertSame(
            count($idData),
            count($enData),
            'Jumlah key id.json dan en.json harus sama persis'
        );
    }

    /**
     * Memastikan tidak ada nilai yang kosong di id.json maupun en.json.
     */
    public function test_no_empty_translation_values(): void
    {
        $idPath = __DIR__ . '/../../lang/id.json';
        $enPath = __DIR__ . '/../../lang/en.json';

        $idData = json_decode(file_get_contents($idPath), true);
        $enData = json_decode(file_get_contents($enPath), true);

        $emptyInId = [];
        foreach ($idData as $key => $val) {
            if (! is_string($val) || trim($val) === '') {
                $emptyInId[] = $key;
            }
        }

        $emptyInEn = [];
        foreach ($enData as $key => $val) {
            if (! is_string($val) || trim($val) === '') {
                $emptyInEn[] = $key;
            }
        }

        $this->assertEmpty($emptyInId, 'Ditemukan nilai kosong di id.json: ' . implode(', ', $emptyInId));
        $this->assertEmpty($emptyInEn, 'Ditemukan nilai kosong di en.json: ' . implode(', ', $emptyInEn));
    }
}
