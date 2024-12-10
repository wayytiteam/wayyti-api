<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'country_name' => 'Afghanistan',
                'symbol' => '؋'
            ],
            [
                'country_name' => 'Albania',
                'symbol' => 'Lek'
            ],
            [
                'country_name' => 'Algeria',
                'symbol' => 'دج'
            ],
            [
                'country_name' => 'Andorra',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Angola',
                'symbol' => 'Kz'
            ],
            [
                'country_name' => 'Antigua and Barbuda',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Argentina',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Armenia',
                'symbol' => '֏'
            ],
            [
                'country_name' => 'Australia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Austria',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Azerbaijan',
                'symbol' => '₼'
            ],
            [
                'country_name' => 'Bahamas',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Bahrain',
                'symbol' => '.د.ب'
            ],
            [
                'country_name' => 'Bangladesh',
                'symbol' => '৳'
            ],
            [
                'country_name' => 'Barbados',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Belarus',
                'symbol' => 'Br'
            ],
            [
                'country_name' => 'Belgium',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Belize',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Benin',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Bhutan',
                'symbol' => 'Nu.'
            ],
            [
                'country_name' => 'Bolivia',
                'symbol' => 'Bs.'
            ],
            [
                'country_name' => 'Bosnia and Herzegovina',
                'symbol' => 'KM'
            ],
            [
                'country_name' => 'Botswana',
                'symbol' => 'P'
            ],
            [
                'country_name' => 'Brazil',
                'symbol' => 'R$'
            ],
            [
                'country_name' => 'Brunei',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Bulgaria',
                'symbol' => 'лв'
            ],
            [
                'country_name' => 'Burkina Faso',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Burundi',
                'symbol' => 'FBu'
            ],
            [
                'country_name' => 'Cabo Verde',
                'symbol' => 'Esc'
            ],
            [
                'country_name' => 'Cambodia',
                'symbol' => '៛'
            ],
            [
                'country_name' => 'Cameroon',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Canada',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Central African Republic',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Chad',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Chile',
                'symbol' => '$'
            ],
            [
                'country_name' => 'China',
                'symbol' => '¥'
            ],
            [
                'country_name' => 'Colombia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Comoros',
                'symbol' => 'CF'
            ],
            [
                'country_name' => 'Congo (Congo-Brazzaville)',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Congo (DRC)',
                'symbol' => 'FC'
            ],
            [
                'country_name' => 'Costa Rica',
                'symbol' => '₡'
            ],
            [
                'country_name' => 'Croatia',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Cuba',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Cyprus',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Czech Republic',
                'symbol' => 'Kč'
            ],
            [
                'country_name' => 'Denmark',
                'symbol' => 'kr'
            ],
            [
                'country_name' => 'Djibouti',
                'symbol' => 'Fdj'
            ],
            [
                'country_name' => 'Dominica',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Dominican Republic',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Ecuador',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Egypt',
                'symbol' => 'E£'
            ],
            [
                'country_name' => 'El Salvador',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Equatorial Guinea',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Eritrea',
                'symbol' => 'Nfk'
            ],
            [
                'country_name' => 'Estonia',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Eswatini',
                'symbol' => 'L'
            ],
            [
                'country_name' => 'Ethiopia',
                'symbol' => 'Br'
            ],
            [
                'country_name' => 'Fiji',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Finland',
                'symbol' => '€'
            ],
            [
                'country_name' => 'France',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Gabon',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Gambia',
                'symbol' => 'D'
            ],
            [
                'country_name' => 'Georgia',
                'symbol' => '₾'
            ],
            [
                'country_name' => 'Germany',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Ghana',
                'symbol' => '₵'
            ],
            [
                'country_name' => 'Greece',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Grenada',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Guatemala',
                'symbol' => 'Q'
            ],
            [
                'country_name' => 'Guinea',
                'symbol' => 'FG'
            ],
            [
                'country_name' => 'Guinea-Bissau',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Guyana',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Haiti',
                'symbol' => 'G'
            ],
            [
                'country_name' => 'Honduras',
                'symbol' => 'L'
            ],
            [
                'country_name' => 'Hungary',
                'symbol' => 'Ft'
            ],
            [
                'country_name' => 'Iceland',
                'symbol' => 'kr'
            ],
            [
                'country_name' => 'India',
                'symbol' => '₹'
            ],
            [
                'country_name' => 'Indonesia',
                'symbol' => 'Rp'
            ],
            [
                'country_name' => 'Iran',
                'symbol' => '﷼'
            ],
            [
                'country_name' => 'Iraq',
                'symbol' => 'ع.د'
            ],
            [
                'country_name' => 'Ireland',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Israel',
                'symbol' => '₪'
            ],
            [
                'country_name' => 'Italy',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Jamaica',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Japan',
                'symbol' => '¥'
            ],
            [
                'country_name' => 'Jordan',
                'symbol' => 'د.ا'
            ],
            [
                'country_name' => 'Kazakhstan',
                'symbol' => '₸'
            ],
            [
                'country_name' => 'Kenya',
                'symbol' => 'KSh'
            ],
            [
                'country_name' => 'Kiribati',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Kuwait',
                'symbol' => 'د.ك'
            ],
            [
                'country_name' => 'Kyrgyzstan',
                'symbol' => 'лв'
            ],
            [
                'country_name' => 'Laos',
                'symbol' => '₭'
            ],
            [
                'country_name' => 'Latvia',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Lebanon',
                'symbol' => 'ل.ل'
            ],
            [
                'country_name' => 'Lesotho',
                'symbol' => 'L'
            ],
            [
                'country_name' => 'Liberia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Libya',
                'symbol' => 'ل.د'
            ],
            [
                'country_name' => 'Liechtenstein',
                'symbol' => 'CHF'
            ],
            [
                'country_name' => 'Lithuania',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Luxembourg',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Madagascar',
                'symbol' => 'Ar'
            ],
            [
                'country_name' => 'Malawi',
                'symbol' => 'MK'
            ],
            [
                'country_name' => 'Malaysia',
                'symbol' => 'RM'
            ],
            [
                'country_name' => 'Maldives',
                'symbol' => 'Rf'
            ],
            [
                'country_name' => 'Mali',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Malta',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Marshall Islands',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Mauritania',
                'symbol' => 'UM'
            ],
            [
                'country_name' => 'Mauritius',
                'symbol' => '₨'
            ],
            [
                'country_name' => 'Mexico',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Micronesia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Moldova',
                'symbol' => 'L'
            ],
            [
                'country_name' => 'Monaco',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Mongolia',
                'symbol' => '₮'
            ],
            [
                'country_name' => 'Montenegro',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Morocco',
                'symbol' => 'MAD'
            ],
            [
                'country_name' => 'Mozambique',
                'symbol' => 'MT'
            ],
            [
                'country_name' => 'Myanmar (Burma)',
                'symbol' => 'K'
            ],
            [
                'country_name' => 'Namibia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Nauru',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Nepal',
                'symbol' => '₨'
            ],
            [
                'country_name' => 'Netherlands',
                'symbol' => '€'
            ],
            [
                'country_name' => 'New Zealand',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Nicaragua',
                'symbol' => 'C$'
            ],
            [
                'country_name' => 'Niger',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Nigeria',
                'symbol' => '₦'
            ],
            [
                'country_name' => 'North Korea',
                'symbol' => '₩'
            ],
            [
                'country_name' => 'North Macedonia',
                'symbol' => 'ден'
            ],
            [
                'country_name' => 'Norway',
                'symbol' => 'kr'
            ],
            [
                'country_name' => 'Oman',
                'symbol' => 'ر.ع.'
            ],
            [
                'country_name' => 'Pakistan',
                'symbol' => '₨'
            ],
            [
                'country_name' => 'Palau',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Palestine',
                'symbol' => '₪'
            ],
            [
                'country_name' => 'Panama',
                'symbol' => 'B/.'
            ],
            [
                'country_name' => 'Papua New Guinea',
                'symbol' => 'K'
            ],
            [
                'country_name' => 'Paraguay',
                'symbol' => '₲'
            ],
            [
                'country_name' => 'Peru',
                'symbol' => 'S/.'
            ],
            [
                'country_name' => 'Philippines',
                'symbol' => '₱'
            ],
            [
                'country_name' => 'Poland',
                'symbol' => 'zł'
            ],
            [
                'country_name' => 'Portugal',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Qatar',
                'symbol' => 'ر.ق'
            ],
            [
                'country_name' => 'Romania',
                'symbol' => 'lei'
            ],
            [
                'country_name' => 'Russia',
                'symbol' => '₽'
            ],
            [
                'country_name' => 'Rwanda',
                'symbol' => 'R₣'
            ],
            [
                'country_name' => 'Saint Kitts and Nevis',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Saint Lucia',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Saint Vincent and the Grenadines',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Samoa',
                'symbol' => 'T'
            ],
            [
                'country_name' => 'San Marino',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Sao Tome and Principe',
                'symbol' => 'Db'
            ],
            [
                'country_name' => 'Saudi Arabia',
                'symbol' => 'ر.س'
            ],
            [
                'country_name' => 'Senegal',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Serbia',
                'symbol' => 'дин.'
            ],
            [
                'country_name' => 'Seychelles',
                'symbol' => '₨'
            ],
            [
                'country_name' => 'Sierra Leone',
                'symbol' => 'Le'
            ],
            [
                'country_name' => 'Singapore',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Slovakia',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Slovenia',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Solomon Islands',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Somalia',
                'symbol' => 'S'
            ],
            [
                'country_name' => 'South Africa',
                'symbol' => 'R'
            ],
            [
                'country_name' => 'South Korea',
                'symbol' => '₩'
            ],
            [
                'country_name' => 'South Sudan',
                'symbol' => '£'
            ],
            [
                'country_name' => 'Spain',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Sri Lanka',
                'symbol' => 'Rs'
            ],
            [
                'country_name' => 'Sudan',
                'symbol' => 'ج.س.'
            ],
            [
                'country_name' => 'Suriname',
                'symbol' => 'S$'
            ],
            [
                'country_name' => 'Sweden',
                'symbol' => 'kr'
            ],
            [
                'country_name' => 'Switzerland',
                'symbol' => 'CHF'
            ],
            [
                'country_name' => 'Syria',
                'symbol' => 'ل.س'
            ],
            [
                'country_name' => 'Taiwan',
                'symbol' => 'NT$'
            ],
            [
                'country_name' => 'Tajikistan',
                'symbol' => 'ЅМ'
            ],
            [
                'country_name' => 'Tanzania',
                'symbol' => 'TSh'
            ],
            [
                'country_name' => 'Thailand',
                'symbol' => '฿'
            ],
            [
                'country_name' => 'Togo',
                'symbol' => 'CFA'
            ],
            [
                'country_name' => 'Tonga',
                'symbol' => 'T$'
            ],
            [
                'country_name' => 'Trinidad and Tobago',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Tunisia',
                'symbol' => 'د.ت'
            ],
            [
                'country_name' => 'Turkey',
                'symbol' => '₺'
            ],
            [
                'country_name' => 'Turkmenistan',
                'symbol' => 'T'
            ],
            [
                'country_name' => 'Tuvalu',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Uganda',
                'symbol' => 'USh'
            ],
            [
                'country_name' => 'Ukraine',
                'symbol' => '₴'
            ],
            [
                'country_name' => 'United Arab Emirates',
                'symbol' => 'د.إ'
            ],
            [
                'country_name' => 'United Kingdom',
                'symbol' => '£'
            ],
            [
                'country_name' => 'United States',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Uruguay',
                'symbol' => '$U'
            ],
            [
                'country_name' => 'Uzbekistan',
                'symbol' => 'лв'
            ],
            [
                'country_name' => 'Vanuatu',
                'symbol' => 'Vt'
            ],
            [
                'country_name' => 'Vatican City',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Venezuela',
                'symbol' => 'Bs.S'
            ],
            [
                'country_name' => 'Vietnam',
                'symbol' => '₫'
            ],
            [
                'country_name' => 'Yemen',
                'symbol' => '﷼'
            ],
            [
                'country_name' => 'Zambia',
                'symbol' => 'ZK'
            ],
            [
                'country_name' => 'Zimbabwe',
                'symbol' => 'Z$'
            ],
            [
                'country_name' => 'Andorra',
                'symbol' => '€'
            ],
            [
                'country_name' => 'Timor-Leste',
                'symbol' => '$'
            ],
            [
                'country_name' => 'Kosovo',
                'symbol' => '€'
            ],
            [
                'country_name' => 'South Ossetia',
                'symbol' => '₽'
            ],
            [
                'country_name' => 'Abkhazia',
                'symbol' => '₽'
            ],
        ];
        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
