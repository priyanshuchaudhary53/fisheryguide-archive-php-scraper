<?php

$cityArr = ["Congleton", "Crewe", "Knutsford", "Macclesfield", "Northwich", "Warrington", "Carlisle", "Penrith", "Bolton", "Wigan", "Rochdale", "Blackburn", "Burnley", "Chorley", "Leyland", "Ormskirk", "Oldham", "Preston", "Liverpool", "Newton le Willows", "St Helens", "Hull", "Ripon", "Selby", "Barnsley", "Doncaster", "Sheffield", "Rotherham", "Bradford", "Huddersfield", "Keighley", "Leeds", "Wakefield", "Morpeth", "Bedford", "Basildon", "Brentwood", "Braintree", "Chelmsford", "Colchester", "Waltham Abbey", "Witham", "Huntingdon", "Peterborough", "St Albans", "Boston", "Grantham", "Skegness", "Kings Lynn", "Norwich", "Bury St Edmunds", "Ipswich", "Romford", "Uxbridge", "Maidenhead", "Reading", "Milton Keynes", "Crowborough", "Hastings", "Uckfield", "Aldershot", "Fareham", "Gosport", "Southampton", "Ashford", "Canterbury", "Banbury", "Bicester", "Guildford", "Godalming", "Crawley", "Horsham", "Newquay", "Truro", "Bideford", "Exeter", "Newton Abbot", "Dorchester", "Gillingham", "Cirencester", "Taunton", "Chippenham", "Salisbury", "Swindon", "Warminster", "Chesterfield", "Daventry", "Kettering", "Wellingborough", "Mansfield", "Stoke-on-Trent", "Tamworth", "Shrewsbury", "Telford", "Nuneaton", "Stratford-Upon-Avon", "Wolverhampton", "Evesham", "Kidderminster"];

$countyArr = ["Cheshire", "Cumbria", "Manchester", "Lancashire", "Merseyside", "East Riding of Yorkshire", "North Yorkshire", "South Yorkshire", "West Yorkshire", "Northumberland", "County Durham", "Tyne and Wear", "Bedfordshire", "Essex", "Cambridgeshire", "Hertfordshire", "Lincolnshire", "Norfolk", "Suffolk", "LONDON", "Berkshire", "Buckinghamshire", "East Sussex", "Hampshire", "Kent", "Oxfordshire", "Surrey", "West Sussex", "Bath and North East Somerset", "Bristol", "Cornwall", "Devon", "Dorset", "Gloucestershire", "Isle of Wight", "Somerset", "Wiltshire", "Derbyshire", "Leicestershire", "Herefordshire", "Northamptonshire", "Nottinghamshire", "Rutland", "Staffordshire", "Shropshire", "Warwickshire", "West Midlands", "Worcestershire"];

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

// Check if title contains county & return county else return null 
function isCounty($title)
{
    global $countyArr;

    foreach ($countyArr as $county) {
        if (stripos($title, $county) !== false) {
            return $county;
        }
    }

    return null;
}

// Check if title contains city & return city else return null
function isCity($title)
{
    global $cityArr;

    foreach ($cityArr as $city) {
        if (stripos($title, $city) !== false) {
            return $city;
        }
    }

    return null;
}

// return county when title contains city
function getCounty($city)
{
    global $locationJson;

    $data = json_decode($locationJson, true);

    foreach ($data as $region => $counties) {
        foreach ($counties as $county => $towns) {
            if (is_array($towns) && in_array($city, $towns)) {
                return $county;
            } elseif (is_string($towns) && $city === $towns) {
                return $county;
            }
        }
    }

    return null;
}

function findLocation($title)
{
    $county = isCounty($title);
    $city = isCity($title);

    if ($county) {
        return $county;
    }

    if ($city) {
        if (getCounty($city)) {
            $county = getCounty($city);
            return $county . '::' . $city;
        } else {
            return $city;
        }
    }
}