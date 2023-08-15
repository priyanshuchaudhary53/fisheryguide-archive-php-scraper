<?php

$locationJson = '{
    "NORTH WEST": {
        "Cheshire": ["Congleton", "Crewe", "Knutsford", "Macclesfield", "Northwich", "Warrington"],
        "Cumbria": ["Carlisle", "Penrith"],
        "Manchester": ["Bolton", "Wigan", "Rochdale"],
        "Lancashire": ["Blackburn", "Burnley", "Chorley", "Leyland", "Ormskirk", "Oldham", "Preston"],
        "Merseyside": ["Liverpool", "Newton le Willows", "St Helens"]
    },
    "NORTH EAST": {
        "Yorkshire": {
            "East Riding of Yorkshire": ["Hull"],
            "North Yorkshire": ["Ripon", "Selby"],
            "South Yorkshire": ["Barnsley", "Doncaster", "Sheffield", "Rotherham"],
            "West Yorkshire": ["Bradford", "Huddersfield", "Keighley", "Leeds", "Wakefield"]
        },
        "Northumberland": ["Morpeth"],
        "County Durham": [],
        "Tyne and Wear": []
    },
    "EAST": {
        "Bedfordshire": ["Bedford"],
        "Essex": ["Basildon", "Brentwood", "Braintree", "Chelmsford", "Colchester", "Waltham Abbey", "Witham"],
        "Cambridgeshire": ["Huntingdon", "Peterborough"],
        "Hertfordshire": ["St Albans"],
        "Lincolnshire": ["Boston", "Grantham", "Skegness"],
        "Norfolk": ["Kings Lynn", "Norwich"],
        "Suffolk": ["Bury St Edmunds", "Ipswich"]
    },
    "LONDON": ["Romford", "Uxbridge"],
    "SOUTH EAST": {
        "Berkshire": ["Maidenhead", "Reading"],
        "Buckinghamshire": ["Milton Keynes"],
        "East Sussex": ["Crowborough", "Hastings", "Uckfield"],
        "Hampshire": ["Aldershot", "Fareham", "Gosport", "Southampton"],
        "Kent": ["Ashford", "Canterbury"],
        "Oxfordshire": ["Banbury", "Bicester"],
        "Surrey": ["Guildford", "Godalming"],
        "West Sussex": ["Crawley", "Horsham"]
    },
    "SOUTH WEST": {
        "Bath and North East Somerset": [],
        "Bristol": [],
        "Cornwall": ["Newquay", "Truro"],
        "Devon": ["Bideford", "Exeter", "Newton Abbot"],
        "Dorset": ["Dorchester", "Gillingham"],
        "Gloucestershire": ["Cirencester"],
        "Isle of Wight": [],
        "Somerset": ["Taunton"],
        "Wiltshire": ["Chippenham", "Salisbury", "Swindon", "Warminster"]
    },
    "MIDLANDS": {
        "Derbyshire": ["Chesterfield"],
        "Leicestershire": [],
        "Herefordshire": [],
        "Northamptonshire": ["Daventry", "Kettering", "Wellingborough"],
        "Nottinghamshire": ["Mansfield"],
        "Rutland": [],
        "Staffordshire": ["Stoke-on-Trent", "Tamworth"],
        "Shropshire": ["Shrewsbury", "Telford"],
        "Warwickshire": ["Nuneaton", "Stratford-Upon-Avon"],
        "West Midlands": ["Wolverhampton"],
        "Worcestershire": ["Evesham", "Kidderminster"]
    }
}';

$location = json_decode($locationJson, true);

function getCity($data, $title)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $result = getCity($value, $title);
            if ($result !== null) {
                return $result;
            }
        } else {
            if (is_string($value) && stripos($title, $value) !== false) {
                return $value;
            }
        }
    }
    return null;
}

function getCounty($data, $city)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $innerKey => $innerValue) {
                if (is_array($innerValue) && in_array($city, $innerValue)) {
                    return $innerKey;
                }
            }
        } elseif (is_array($value) && in_array($city, $value)) {
            return $key;
        }
    }

    return null;
}