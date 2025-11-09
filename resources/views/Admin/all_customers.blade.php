@extends('layouts.admin_master')
@section('content')
    <style>
        #add-material {
            color: #007bff;
            /* Bootstrap primary color */
            background-color: transparent !important;
            /* Override any background color */
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Customers
        </div>
        <div class="card-dashboard-sub-header">
            <div class="card-dashboard-sub-header-controls">
                <div class="float-right">
                <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
                    data-target="#addCustomerModal">
                    <i class="fas fa-plus"></i> Add Customer
                </button>                                                           
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Date Added</th>
                            <th>Last Sale</th>
                            <th>Sales</th>
                            <th>Total Spent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $row)
                            <tr>
                                <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->type }}</td>
                                <td>{{ $row->last_sale }}</td>
                                <td>{{ $row->last_sale }}</td>
                                <td>3</td>
                                <td>{{ $row->total }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editCategoryModal"
                                        data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-type="{{ $row->type }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn delete-category" data-id="{{ $row->id }}" data-type="{{ $row->type }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ url('/add-customer') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="first_name">First Name</label>
                                    <input class="form-control" name="first_name" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="last_name">Last Name</label>
                                    <input class="form-control" name="last_name" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1" for="company_name">Company</label>
                                    <input class="form-control" name="company_name" type="text" placeholder=""
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="email">Email</label>
                                    <input class="form-control" name="email" type="text" placeholder="" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="phone">Phone</label>
                                    <input class="form-control" name="phone" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1" for="address1">Address Line 1</label>
                                    <input class="form-control" name="address1" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1" for="address2">Address Line 2</label>
                                    <input class="form-control" name="address2" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1" for="city">City</label>
                                    <input class="form-control" name="city" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="state">State</label>
                                    <input class="form-control" name="state" type="text" placeholder="" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="zip_code">ZIP Code</label>
                                    <input class="form-control" name="zip_code" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1" for="notes">Country</label>
                                    <select class="form-control" id="country_code" name="country_code">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />

    <script>
        // Define the countries array
        var countries = [
            {'value' : 'AF', 'label' : 'Afghanistan'},
            {'value' : 'AX', 'label' : 'Aland Islands'},
            {'value' : 'AL', 'label' : 'Albania'},
            {'value' : 'DZ', 'label' : 'Algeria'},
            {'value' : 'AS', 'label' : 'American Samoa'},
            {'value' : 'AD', 'label' : 'Andorra'},
            {'value' : 'AO', 'label' : 'Angola'},
            {'value' : 'AI', 'label' : 'Anguilla'},
            {'value' : 'AQ', 'label' : 'Antarctica'},
            {'value' : 'AG', 'label' : 'Antigua and Barbuda'},
            {'value' : 'AR', 'label' : 'Argentina'},
            {'value' : 'AM', 'label' : 'Armenia'},
            {'value' : 'AW', 'label' : 'Aruba'},
            {'value' : 'AU', 'label' : 'Australia'},
            {'value' : 'AT', 'label' : 'Austria'},
            {'value' : 'AZ', 'label' : 'Azerbaijan'},
            {'value' : 'BS', 'label' : 'Bahamas'},
            {'value' : 'BH', 'label' : 'Bahrain'},
            {'value' : 'BD', 'label' : 'Bangladesh'},
            {'value' : 'BB', 'label' : 'Barbados'},
            {'value' : 'BY', 'label' : 'Belarus'},
            {'value' : 'BE', 'label' : 'Belgium'},
            {'value' : 'BZ', 'label' : 'Belize'},
            {'value' : 'BJ', 'label' : 'Benin'},
            {'value' : 'BM', 'label' : 'Bermuda'},
            {'value' : 'BT', 'label' : 'Bhutan'},
            {'value' : 'BO', 'label' : 'Bolivia'},
            {'value' : 'BQ', 'label' : 'Bonaire, Sint Eustatius and Saba'},
            {'value' : 'BA', 'label' : 'Bosnia and Herzegovina'},
            {'value' : 'BW', 'label' : 'Botswana'},
            {'value' : 'BV', 'label' : 'Bouvet Island'},
            {'value' : 'BR', 'label' : 'Brazil'},
            {'value' : 'IO', 'label' : 'British Indian Ocean Territory'},
            {'value' : 'BN', 'label' : 'Brunei Darussalam'},
            {'value' : 'BG', 'label' : 'Bulgaria'},
            {'value' : 'BF', 'label' : 'Burkina Faso'},
            {'value' : 'BI', 'label' : 'Burundi'},
            {'value' : 'KH', 'label' : 'Cambodia'},
            {'value' : 'CM', 'label' : 'Cameroon'},
            {'value' : 'CA', 'label' : 'Canada'},
            {'value' : 'CV', 'label' : 'Cape Verde'},
            {'value' : 'KY', 'label' : 'Cayman Islands'},
            {'value' : 'CF', 'label' : 'Central African Republic'},
            {'value' : 'TD', 'label' : 'Chad'},
            {'value' : 'CL', 'label' : 'Chile'},
            {'value' : 'CN', 'label' : 'China'},
            {'value' : 'CX', 'label' : 'Christmas Island'},
            {'value' : 'CC', 'label' : 'Cocos (Keeling) Islands'},
            {'value' : 'CO', 'label' : 'Colombia'},
            {'value' : 'KM', 'label' : 'Comoros'},
            {'value' : 'CG', 'label' : 'Congo'},
            {'value' : 'CD', 'label' : 'Congo, Democratic Republic of the Congo'},
            {'value' : 'CK', 'label' : 'Cook Islands'},
            {'value' : 'CR', 'label' : 'Costa Rica'},
            {'value' : 'CI', 'label' : "Cote D'Ivoire"},
            {'value' : 'HR', 'label' : 'Croatia'},
            {'value' : 'CU', 'label' : 'Cuba'},
            {'value' : 'CW', 'label' : 'Curacao'},
            {'value' : 'CY', 'label' : 'Cyprus'},
            {'value' : 'CZ', 'label' : 'Czech Republic'},
            {'value' : 'DK', 'label' : 'Denmark'},
            {'value' : 'DJ', 'label' : 'Djibouti'},
            {'value' : 'DM', 'label' : 'Dominica'},
            {'value' : 'DO', 'label' : 'Dominican Republic'},
            {'value' : 'EC', 'label' : 'Ecuador'},
            {'value' : 'EG', 'label' : 'Egypt'},
            {'value' : 'SV', 'label' : 'El Salvador'},
            {'value' : 'GQ', 'label' : 'Equatorial Guinea'},
            {'value' : 'ER', 'label' : 'Eritrea'},
            {'value' : 'EE', 'label' : 'Estonia'},
            {'value' : 'ET', 'label' : 'Ethiopia'},
            {'value' : 'FK', 'label' : 'Falkland Islands (Malvinas)'},
            {'value' : 'FO', 'label' : 'Faroe Islands'},
            {'value' : 'FJ', 'label' : 'Fiji'},
            {'value' : 'FI', 'label' : 'Finland'},
            {'value' : 'FR', 'label' : 'France'},
            {'value' : 'GF', 'label' : 'French Guiana'},
            {'value' : 'PF', 'label' : 'French Polynesia'},
            {'value' : 'TF', 'label' : 'French Southern Territories'},
            {'value' : 'GA', 'label' : 'Gabon'},
            {'value' : 'GM', 'label' : 'Gambia'},
            {'value' : 'GE', 'label' : 'Georgia'},
            {'value' : 'DE', 'label' : 'Germany'},
            {'value' : 'GH', 'label' : 'Ghana'},
            {'value' : 'GI', 'label' : 'Gibraltar'},
            {'value' : 'GR', 'label' : 'Greece'},
            {'value' : 'GL', 'label' : 'Greenland'},
            {'value' : 'GD', 'label' : 'Grenada'},
            {'value' : 'GP', 'label' : 'Guadeloupe'},
            {'value' : 'GU', 'label' : 'Guam'},
            {'value' : 'GT', 'label' : 'Guatemala'},
            {'value' : 'GG', 'label' : 'Guernsey'},
            {'value' : 'GN', 'label' : 'Guinea'},
            {'value' : 'GW', 'label' : 'Guinea-Bissau'},
            {'value' : 'GY', 'label' : 'Guyana'},
            {'value' : 'HT', 'label' : 'Haiti'},
            {'value' : 'HM', 'label' : 'Heard Island and Mcdonald Islands'},
            {'value' : 'VA', 'label' : 'Holy See (Vatican City State)'},
            {'value' : 'HN', 'label' : 'Honduras'},
            {'value' : 'HK', 'label' : 'Hong Kong'},
            {'value' : 'HU', 'label' : 'Hungary'},
            {'value' : 'IS', 'label' : 'Iceland'},
            {'value' : 'IN', 'label' : 'India'},
            {'value' : 'ID', 'label' : 'Indonesia'},
            {'value' : 'IR', 'label' : 'Iran, Islamic Republic of'},
            {'value' : 'IQ', 'label' : 'Iraq'},
            {'value' : 'IE', 'label' : 'Ireland'},
            {'value' : 'IM', 'label' : 'Isle of Man'},
            {'value' : 'IL', 'label' : 'Israel'},
            {'value' : 'IT', 'label' : 'Italy'},
            {'value' : 'JM', 'label' : 'Jamaica'},
            {'value' : 'JP', 'label' : 'Japan'},
            {'value' : 'JE', 'label' : 'Jersey'},
            {'value' : 'JO', 'label' : 'Jordan'},
            {'value' : 'KZ', 'label' : 'Kazakhstan'},
            {'value' : 'KE', 'label' : 'Kenya'},
            {'value' : 'KI', 'label' : 'Kiribati'},
            {'value' : 'KP', 'label' : "Korea, Democratic People's Republic of"},
            {'value' : 'KR', 'label' : 'Korea, Republic of'},
            {'value' : 'XK', 'label' : 'Kosovo'},
            {'value' : 'KW', 'label' : 'Kuwait'},
            {'value' : 'KG', 'label' : 'Kyrgyzstan'},
            {'value' : 'LA', 'label' : "Lao People's Democratic Republic"},
            {'value' : 'LV', 'label' : 'Latvia'},
            {'value' : 'LB', 'label' : 'Lebanon'},
            {'value' : 'LS', 'label' : 'Lesotho'},
            {'value' : 'LR', 'label' : 'Liberia'},
            {'value' : 'LY', 'label' : 'Libyan Arab Jamahiriya'},
            {'value' : 'LI', 'label' : 'Liechtenstein'},
            {'value' : 'LT', 'label' : 'Lithuania'},
            {'value' : 'LU', 'label' : 'Luxembourg'},
            {'value' : 'MO', 'label' : 'Macao'},
            {'value' : 'MK', 'label' : 'Macedonia, the Former Yugoslav Republic of'},
            {'value' : 'MG', 'label' : 'Madagascar'},
            {'value' : 'MW', 'label' : 'Malawi'},
            {'value' : 'MY', 'label' : 'Malaysia'},
            {'value' : 'MV', 'label' : 'Maldives'},
            {'value' : 'ML', 'label' : 'Mali'},
            {'value' : 'MT', 'label' : 'Malta'},
            {'value' : 'MH', 'label' : 'Marshall Islands'},
            {'value' : 'MQ', 'label' : 'Martinique'},
            {'value' : 'MR', 'label' : 'Mauritania'},
            {'value' : 'MU', 'label' : 'Mauritius'},
            {'value' : 'YT', 'label' : 'Mayotte'},
            {'value' : 'MX', 'label' : 'Mexico'},
            {'value' : 'FM', 'label' : 'Micronesia, Federated States of'},
            {'value' : 'MD', 'label' : 'Moldova, Republic of'},
            {'value' : 'MC', 'label' : 'Monaco'},
            {'value' : 'MN', 'label' : 'Mongolia'},
            {'value' : 'ME', 'label' : 'Montenegro'},
            {'value' : 'MS', 'label' : 'Montserrat'},
            {'value' : 'MA', 'label' : 'Morocco'},
            {'value' : 'MZ', 'label' : 'Mozambique'},
            {'value' : 'MM', 'label' : 'Myanmar'},
            {'value' : 'NA', 'label' : 'Namibia'},
            {'value' : 'NR', 'label' : 'Nauru'},
            {'value' : 'NP', 'label' : 'Nepal'},
            {'value' : 'NL', 'label' : 'Netherlands'},
            {'value' : 'AN', 'label' : 'Netherlands Antilles'},
            {'value' : 'NC', 'label' : 'New Caledonia'},
            {'value' : 'NZ', 'label' : 'New Zealand'},
            {'value' : 'NI', 'label' : 'Nicaragua'},
            {'value' : 'NE', 'label' : 'Niger'},
            {'value' : 'NG', 'label' : 'Nigeria'},
            {'value' : 'NU', 'label' : 'Niue'},
            {'value' : 'NF', 'label' : 'Norfolk Island'},
            {'value' : 'MP', 'label' : 'Northern Mariana Islands'},
            {'value' : 'NO', 'label' : 'Norway'},
            {'value' : 'OM', 'label' : 'Oman'},
            {'value' : 'PK', 'label' : 'Pakistan'},
            {'value' : 'PW', 'label' : 'Palau'},
            {'value' : 'PS', 'label' : 'Palestinian Territory, Occupied'},
            {'value' : 'PA', 'label' : 'Panama'},
            {'value' : 'PG', 'label' : 'Papua New Guinea'},
            {'value' : 'PY', 'label' : 'Paraguay'},
            {'value' : 'PE', 'label' : 'Peru'},
            {'value' : 'PH', 'label' : 'Philippines'},
            {'value' : 'PN', 'label' : 'Pitcairn'},
            {'value' : 'PL', 'label' : 'Poland'},
            {'value' : 'PT', 'label' : 'Portugal'},
            {'value' : 'PR', 'label' : 'Puerto Rico'},
            {'value' : 'QA', 'label' : 'Qatar'},
            {'value' : 'RE', 'label' : 'Reunion'},
            {'value' : 'RO', 'label' : 'Romania'},
            {'value' : 'RU', 'label' : 'Russian Federation'},
            {'value' : 'RW', 'label' : 'Rwanda'},
            {'value' : 'BL', 'label' : 'Saint Barthelemy'},
            {'value' : 'SH', 'label' : 'Saint Helena'},
            {'value' : 'KN', 'label' : 'Saint Kitts and Nevis'},
            {'value' : 'LC', 'label' : 'Saint Lucia'},
            {'value' : 'MF', 'label' : 'Saint Martin'},
            {'value' : 'PM', 'label' : 'Saint Pierre and Miquelon'},
            {'value' : 'VC', 'label' : 'Saint Vincent and the Grenadines'},
            {'value' : 'WS', 'label' : 'Samoa'},
            {'value' : 'SM', 'label' : 'San Marino'},
            {'value' : 'ST', 'label' : 'Sao Tome and Principe'},
            {'value' : 'SA', 'label' : 'Saudi Arabia'},
            {'value' : 'SN', 'label' : 'Senegal'},
            {'value' : 'RS', 'label' : 'Serbia'},
            {'value' : 'SC', 'label' : 'Seychelles'},
            {'value' : 'SL', 'label' : 'Sierra Leone'},
            {'value' : 'SG', 'label' : 'Singapore'},
            {'value' : 'SX', 'label' : 'Sint Maarten'},
            {'value' : 'SK', 'label' : 'Slovakia'},
            {'value' : 'SI', 'label' : 'Slovenia'},
            {'value' : 'SB', 'label' : 'Solomon Islands'},
            {'value' : 'SO', 'label' : 'Somalia'},
            {'value' : 'ZA', 'label' : 'South Africa'},
            {'value' : 'GS', 'label' : 'South Georgia and the South Sandwich Islands'},
            {'value' : 'SS', 'label' : 'South Sudan'},
            {'value' : 'ES', 'label' : 'Spain'},
            {'value' : 'LK', 'label' : 'Sri Lanka'},
            {'value' : 'SD', 'label' : 'Sudan'},
            {'value' : 'SR', 'label' : 'Suriname'},
            {'value' : 'SJ', 'label' : 'Svalbard and Jan Mayen'},
            {'value' : 'SZ', 'label' : 'Swaziland'},
            {'value' : 'SE', 'label' : 'Sweden'},
            {'value' : 'CH', 'label' : 'Switzerland'},
            {'value' : 'SY', 'label' : 'Syrian Arab Republic'},
            {'value' : 'TW', 'label' : 'Taiwan, Province of China'},
            {'value' : 'TJ', 'label' : 'Tajikistan'},
            {'value' : 'TZ', 'label' : 'Tanzania, United Republic of'},
            {'value' : 'TH', 'label' : 'Thailand'},
            {'value' : 'TL', 'label' : 'Timor-Leste'},
            {'value' : 'TG', 'label' : 'Togo'},
            {'value' : 'TK', 'label' : 'Tokelau'},
            {'value' : 'TO', 'label' : 'Tonga'},
            {'value' : 'TT', 'label' : 'Trinidad and Tobago'},
            {'value' : 'TN', 'label' : 'Tunisia'},
            {'value' : 'TR', 'label' : 'Turkey'},
            {'value' : 'TM', 'label' : 'Turkmenistan'},
            {'value' : 'TC', 'label' : 'Turks and Caicos Islands'},
            {'value' : 'TV', 'label' : 'Tuvalu'},
            {'value' : 'UG', 'label' : 'Uganda'},
            {'value' : 'UA', 'label' : 'Ukraine'},
            {'value' : 'AE', 'label' : 'United Arab Emirates'},
            {'value' : 'GB', 'label' : 'United Kingdom'},
            {'value' : 'US', 'label' : 'United States'},
            {'value' : 'UM', 'label' : 'United States Minor Outlying Islands'},
            {'value' : 'UY', 'label' : 'Uruguay'},
            {'value' : 'UZ', 'label' : 'Uzbekistan'},
            {'value' : 'VU', 'label' : 'Vanuatu'},
            {'value' : 'VE', 'label' : 'Venezuela'},
            {'value' : 'VN', 'label' : 'Vietnam'},
            {'value' : 'VG', 'label' : 'Virgin Islands, British'},
            {'value' : 'VI', 'label' : 'Virgin Islands, U.S.'},
            {'value' : 'WF', 'label' : 'Wallis and Futuna'},
            {'value' : 'EH', 'label' : 'Western Sahara'},
            {'value' : 'YE', 'label' : 'Yemen'},
            {'value' : 'ZM', 'label' : 'Zambia'},
            {'value' : 'ZW', 'label' : 'Zimbabwe'}
        ];

        // Get the select element
        var selectElement = document.getElementById('country_code');

        // Loop through the countries array and create option elements
        countries.forEach(function(country) {
            var option = document.createElement('option');
            option.value = country.value;
            option.textContent = country.label;
            selectElement.appendChild(option);
        });
    </script>
@endsection
