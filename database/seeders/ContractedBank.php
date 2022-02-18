<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractedBank extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bankList = \App\Models\Bank::all()->groupBy('swift');

        $banks = [
            [
                'banks_id' => $bankList['TCZBTR2A']->first()->id,
                'iban' => 'TR780001002087975141005001',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => false,
            ],
            [
                'banks_id' => $bankList['TVBATR2A']->first()->id,
                'iban' => 'TR560001500158007314343977',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ],
            [
                'banks_id' => $bankList['YAPITRISFEX']->first()->id,
                'iban' => 'TR720006701000000079676542',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ],
            [
                'banks_id' => $bankList['TGBATRIS']->first()->id,
                'iban' => 'TR460006200077000006291925',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ],
            [
                'banks_id' => $bankList['KTEFTRIS']->first()->id,
                'iban' => 'TR340020500009748487400001',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ],
            [
                'banks_id' => $bankList['ODEATRIS']->first()->id,
                'iban' => 'TR560014600000147141900001',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ],
            [
                'banks_id' => $bankList['TEBUTRIS']->first()->id,
                'iban' => 'TR260003200000000094825295',
                'account_name' => 'RAYSOFT YAZILIM VE BİLİŞİM SANAYİ VE TİCARET ANONİM ŞİRKETİ',
                'sil' => true,
            ]
        ];

        foreach ($banks as $bank) {
            $sil = $bank['sil'];
            unset($bank['sil']);
            \App\Models\ContractedBank::create($bank);
            if ($sil) {
                \App\Models\ContractedBank::where('iban', $bank['iban'])->delete();
            }
        }

    }
}
