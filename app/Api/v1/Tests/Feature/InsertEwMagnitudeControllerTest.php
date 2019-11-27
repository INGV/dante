<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InsertEwMagnitudeControllerTest extends TestCase
{
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters_json = '{
        "data": {
            "ewLogo": {
                "type": "TYPE_MAGNITUDE",
                "module": "MOD_LOCALMAG_PREL",
                "installation": "INST_INGV",
                "instance": "hew10_mole",
                "user": "ew",
                "hostname": "hew10"
            },
            "ewMessage": {
                "quakeId": 205340,
                "version": "ew prelim",
                "mag": 1.87,
                "error": 0.29,
                "quality": 0.85,
                "minDist": 9.43,
                "azimuth": -1,
                "nStations": 7,
                "nChannels": 16,
                "qAuthor": "014101073:028129073",
                "qddsVersion": 0,
                "iMagType": 1,
                "magType": "ML",
                "algorithm": "MED",
                "ingvQuality": "A",
                "phases": [
                    {
                        "sta": "GSCL",
                        "comp": "HHE",
                        "net": "GU",
                        "loc": "--",
                        "mag": 2.21,
                        "dist": 9.43,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:28.150000",
                        "amp1": -0.668,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:28.260000",
                        "amp2": 0.881,
                        "period2": -1
                    },
                    {
                        "sta": "GSCL",
                        "comp": "HHN",
                        "net": "GU",
                        "loc": "--",
                        "mag": 2.34,
                        "dist": 9.43,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:28.720000",
                        "amp1": -1.41,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:28.190000",
                        "amp2": 0.658,
                        "period2": -1
                    },
                    {
                        "sta": "ZCCA",
                        "comp": "HNE",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.12,
                        "dist": 25.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:32.390000",
                        "amp1": -0.414,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.210000",
                        "amp2": 0.526,
                        "period2": -1
                    },
                    {
                        "sta": "ZCCA",
                        "comp": "HNN",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.52,
                        "dist": 25.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:32.280000",
                        "amp1": -1.41,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.480000",
                        "amp2": 0.944,
                        "period2": -1
                    },
                    {
                        "sta": "ZCCA",
                        "comp": "HHE",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.12,
                        "dist": 25.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:32.380000",
                        "amp1": -0.502,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.200000",
                        "amp2": 0.443,
                        "period2": -1
                    },
                    {
                        "sta": "ZCCA",
                        "comp": "HHN",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.51,
                        "dist": 25.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:32.280000",
                        "amp1": -1.39,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.480000",
                        "amp2": 0.944,
                        "period2": -1
                    },
                    {
                        "sta": "POPM",
                        "comp": "HHE",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.65,
                        "dist": 41.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:34.320000",
                        "amp1": -0.171,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.110000",
                        "amp2": 0.061,
                        "period2": -1
                    },
                    {
                        "sta": "POPM",
                        "comp": "HHN",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.57,
                        "dist": 41.09,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:35.920000",
                        "amp1": -0.0957,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.710000",
                        "amp2": 0.1,
                        "period2": -1
                    },
                    {
                        "sta": "CARD",
                        "comp": "HHE",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.74,
                        "dist": 45.29,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:35.770000",
                        "amp1": -0.136,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.840000",
                        "amp2": 0.121,
                        "period2": -1
                    },
                    {
                        "sta": "CARD",
                        "comp": "HHN",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.71,
                        "dist": 45.29,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:35.940000",
                        "amp1": -0.15,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.490000",
                        "amp2": 0.0934,
                        "period2": -1
                    },
                    {
                        "sta": "GRAM",
                        "comp": "HHE",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.82,
                        "dist": 49.15,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:37.460000",
                        "amp1": -0.111,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:37.280000",
                        "amp2": 0.17,
                        "period2": -1
                    },
                    {
                        "sta": "GRAM",
                        "comp": "HHN",
                        "net": "GU",
                        "loc": "--",
                        "mag": 1.93,
                        "dist": 49.15,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:36.690000",
                        "amp1": -0.194,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:36.990000",
                        "amp2": 0.172,
                        "period2": -1
                    },
                    {
                        "sta": "CAVE",
                        "comp": "HHE",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.21,
                        "dist": 57.05,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:37.720000",
                        "amp1": -0.374,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:38.170000",
                        "amp2": 0.242,
                        "period2": -1
                    },
                    {
                        "sta": "CAVE",
                        "comp": "HHN",
                        "net": "IV",
                        "loc": "--",
                        "mag": 2.21,
                        "dist": 57.05,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:37.980000",
                        "amp1": -0.239,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:37.720000",
                        "amp2": 0.376,
                        "period2": -1
                    },
                    {
                        "sta": "MOCL",
                        "comp": "EHE",
                        "net": "IV",
                        "loc": "--",
                        "mag": 1.77,
                        "dist": 60.61,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:42.310000",
                        "amp1": -0.13,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:42.500000",
                        "amp2": 0.0729,
                        "period2": -1
                    },
                    {
                        "sta": "MOCL",
                        "comp": "EHN",
                        "net": "IV",
                        "loc": "--",
                        "mag": 1.73,
                        "dist": 60.61,
                        "corr": 0,
                        "time1": "2019-11-27 00:04:42.450000",
                        "amp1": -0.113,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:42.150000",
                        "amp2": 0.0744,
                        "period2": -1
                    }
                ]
            }
        }
    }';

    /* Output structure expected */
    protected $data_json = '{
        "magnitudes": [
            {
                "id": 68846025,
                "mag": 1.87,
                "err": 0.29,
                "quality": 0.85,
                "min_dist": 9.43,
                "azimut": -1,
                "nsta": 7,
                "ncha": 16,
                "nsta_used": null,
                "mag_quality": "A",
                "modified": "2019-11-27 16:01:21",
                "inserted": "2019-11-27 16:01:21",
                "fk_hypocenter": 66642941,
                "fk_type_magnitude": 5,
                "fk_provenance": 1790,
                "amplitudes": [
                    {
                        "time1": "2019-11-27 00:04:28.150000",
                        "amp1": -0.668,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:28.260000",
                        "amp2": 0.881,
                        "period2": -1,
                        "fk_scnl": 51541,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659764,
                        "st_amp_mag": {
                            "id": 476525047,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659764,
                            "ep_distance": 9.43,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.21,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:28.720000",
                        "amp1": -1.41,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:28.190000",
                        "amp2": 0.658,
                        "period2": -1,
                        "fk_scnl": 51531,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659765,
                        "st_amp_mag": {
                            "id": 476525048,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659765,
                            "ep_distance": 9.43,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.34,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:32.390000",
                        "amp1": -0.414,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.210000",
                        "amp2": 0.526,
                        "period2": -1,
                        "fk_scnl": 1885,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659766,
                        "st_amp_mag": {
                            "id": 476525049,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659766,
                            "ep_distance": 25.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.12,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:32.280000",
                        "amp1": -1.41,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.480000",
                        "amp2": 0.944,
                        "period2": -1,
                        "fk_scnl": 1887,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659767,
                        "st_amp_mag": {
                            "id": 476525050,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659767,
                            "ep_distance": 25.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.52,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:32.380000",
                        "amp1": -0.502,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.200000",
                        "amp2": 0.443,
                        "period2": -1,
                        "fk_scnl": 1179,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659768,
                        "st_amp_mag": {
                            "id": 476525051,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659768,
                            "ep_distance": 25.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.12,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:32.280000",
                        "amp1": -1.39,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:32.480000",
                        "amp2": 0.944,
                        "period2": -1,
                        "fk_scnl": 1181,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659769,
                        "st_amp_mag": {
                            "id": 476525052,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659769,
                            "ep_distance": 25.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.51,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:34.320000",
                        "amp1": -0.171,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.110000",
                        "amp2": 0.061,
                        "period2": -1,
                        "fk_scnl": 2067,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659770,
                        "st_amp_mag": {
                            "id": 476525053,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659770,
                            "ep_distance": 41.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.65,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:35.920000",
                        "amp1": -0.0957,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.710000",
                        "amp2": 0.1,
                        "period2": -1,
                        "fk_scnl": 2069,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659771,
                        "st_amp_mag": {
                            "id": 476525054,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659771,
                            "ep_distance": 41.09,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.57,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:35.770000",
                        "amp1": -0.136,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.840000",
                        "amp2": 0.121,
                        "period2": -1,
                        "fk_scnl": 7921,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659772,
                        "st_amp_mag": {
                            "id": 476525055,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659772,
                            "ep_distance": 45.29,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.74,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:35.940000",
                        "amp1": -0.15,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:35.490000",
                        "amp2": 0.0934,
                        "period2": -1,
                        "fk_scnl": 7941,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659773,
                        "st_amp_mag": {
                            "id": 476525056,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659773,
                            "ep_distance": 45.29,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.71,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:37.460000",
                        "amp1": -0.111,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:37.280000",
                        "amp2": 0.17,
                        "period2": -1,
                        "fk_scnl": 7711,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659774,
                        "st_amp_mag": {
                            "id": 476525057,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659774,
                            "ep_distance": 49.15,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.82,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:36.690000",
                        "amp1": -0.194,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:36.990000",
                        "amp2": 0.172,
                        "period2": -1,
                        "fk_scnl": 7791,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659775,
                        "st_amp_mag": {
                            "id": 476525058,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659775,
                            "ep_distance": 49.15,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.93,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:37.720000",
                        "amp1": -0.374,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:38.170000",
                        "amp2": 0.242,
                        "period2": -1,
                        "fk_scnl": 7291,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659776,
                        "st_amp_mag": {
                            "id": 476525059,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659776,
                            "ep_distance": 57.05,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.21,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:37.980000",
                        "amp1": -0.239,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:37.720000",
                        "amp2": 0.376,
                        "period2": -1,
                        "fk_scnl": 7251,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659777,
                        "st_amp_mag": {
                            "id": 476525060,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659777,
                            "ep_distance": 57.05,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 2.21,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:42.310000",
                        "amp1": -0.13,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:42.500000",
                        "amp2": 0.0729,
                        "period2": -1,
                        "fk_scnl": 172061,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659778,
                        "st_amp_mag": {
                            "id": 476525061,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659778,
                            "ep_distance": 60.61,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.77,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    },
                    {
                        "time1": "2019-11-27 00:04:42.450000",
                        "amp1": -0.113,
                        "period1": -1,
                        "time2": "2019-11-27 00:04:42.150000",
                        "amp2": 0.0744,
                        "period2": -1,
                        "fk_scnl": 172071,
                        "fk_provenance": 1790,
                        "fk_type_amplitude": 2,
                        "modified": "2019-11-27 16:01:21",
                        "inserted": "2019-11-27 16:01:21",
                        "id": 418659779,
                        "st_amp_mag": {
                            "id": 476525062,
                            "fk_magnitude": 68846025,
                            "fk_amplitude": 418659779,
                            "ep_distance": 60.61,
                            "hyp_distance": null,
                            "azimut": null,
                            "mag": 1.73,
                            "err_mag": null,
                            "mag_correction": 0,
                            "is_used": null,
                            "fk_type_magnitude": 5,
                            "modified": "2019-11-27 16:01:21",
                            "inserted": "2019-11-27 16:01:21"
                        }
                    }
                ]
            }
        ]
    }';
    
    public function setUp(): void 
    {
        parent::setUp();
        
        /* Set '$inputParameters' using '$inputParameters_json' */
        $inputParameters_json__decoded = json_decode($this->inputParameters_json, true);
        $this->inputParameters = $inputParameters_json__decoded;
        
        /* Set a valid 'Pat' value */
        //foreach ($this->inputParameters['data']['ewMessage']['phases'] as &$value) {
        //    $value['Pat'] = date("Y-m-d H:i:s").'.'.rand(100, 999);
        //}
        
        /* set JSON data structure into $this->data */
        $DanteBaseTest = new DanteBaseTest();        
        $data_json__decoded = json_decode($this->data_json, true);    
        $data_json__structure = $DanteBaseTest->getArrayStructure($data_json__decoded);
        $this->data_structure = $data_json__structure;
    }
    
    public function test_store_json() 
    {
        $response = $this->post(route('insert_ew_magnitude.store', $this->inputParameters));
        $response->assertStatus(201);
        
        /* Get output data */
        $data = json_decode($response->getContent(), true);
        //print_r($data);

        /* Check JSON structure */
        $response->assertJsonStructure($this->data_structure);

        /*** START - Remove all inserted data ***/       
        /* Remove 'magnitudes' */
        foreach ($data['magnitudes'] as $magnitude) {
            /* Remove 'amplitudes' */
            foreach ($magnitude['amplitudes'] as $amplitude) {
                /* Remove 'st_amp_mag' */
                $this->delete(route('st_amp_mag.destroy', $amplitude['st_amp_mag']['id']))->assertStatus(204);
                /* Remove 'amplitude' */
                $this->delete(route('amplitude.destroy', $amplitude['id']))->assertStatus(204);
            }            
            $this->delete(route('magnitude.destroy', $magnitude['id']))->assertStatus(204);
        }
        /*** END - Remove all inserted data ***/
    }
}
