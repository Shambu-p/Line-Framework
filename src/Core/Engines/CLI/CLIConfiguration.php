<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 3/1/2021
 * Time: 8:20 PM
 */

namespace Absoft\Line\Core\Engines\CLI;


class CLIConfiguration
{

    public static $map = [
        "version" => [],
        "help" => [
            [
                "name" => "command",
                "importance" => false,
                "description" => "name of the command you want to get the description of. \n if this argument not set then all the commands and their description will be shown",
            ],
        ],
        "create" => [
            [
                "name" => "type",
                "importance" => true,
                "description" => "type of file to be created \n -b to generate builder file \n -c to generate controller file \n -m to generate model file \n -bmc to generate builder, model and controllr files",
            ],
            [
                "name" => "name",
                "importance" => true,
                "description" => "name of the class",
            ]
        ],
        "delete" => [
            [
                "name" => "type",
                "importance" => true,
                "description" => "type of file to be removed \n -b to remove builder file \n -c to remove controller file \n -m to remove model file \n -i to remove initializer file",
            ],
            [
                "name" => "name",
                "importance" => true,
                "description" => "name of the class",
            ]
        ],
        "startServer" => [
            [
                "name" => "host",
                "importance" => false,
                "description" => "address of this machine which will be used by clients to locate it.",
            ]
        ],
        "run" => [
            [
                "name" => "host",
                "importance" => false,
                "description" => "address of this machine which will be used by clients to locate it.",
            ]
        ],
        "init" => [
            [
                "name" => "builder",
                "importance" => true,
                "description" => "The table name which will be filled with the initialization data.",
            ]
        ],
        "crcmd" => [
            [
                "name" => "name",
                "importance" => true,
                "description" => "the name of the command",
            ],
            [
                "name" => "receiver",
                "importance" => true,
                "description" => "the name of class that does the actual work",
            ],
            [
                "name" => "method",
                "importance" => true,
                "description" => "the name of the method that does the actual work",
            ]
        ],
        "export" => [
            [
                "name" => "builder",
                "importance" => true,
                "description" => "name of builder that is going to be exported",
            ]
        ],
        "drop" => [
            [
                "name" => "builder",
                "importance" => true,
                "description" => "name of builder that is going to be dropped",
            ]
        ]
    ];

}
