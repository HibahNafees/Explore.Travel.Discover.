<?php

/**
 * Seed data for the `destinations` table (used by `php bin/migrate.php --seed`).
 * The first six are the original packages from the home page.
 */
return [
    [
        'slug' => 'mumbai', 'name' => 'Mumbai', 'country' => 'India', 'region' => 'Asia',
        'summary' => 'Colonial landmarks, seaside promenades and the best street food in India.',
        'description' => "Walk from the Gateway of India to the Art Deco façades of Marine Drive, take a ferry to the Elephanta Caves, and eat your way through vada pav, pav bhaji and bhel puri on Juhu Beach.\n\nThe package includes guided city walks, a Bollywood studio visit and a sunset harbour cruise.",
        'image' => 'images/p-1.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 5, 'rating' => 4.0,
        'latitude' => 19.0760, 'longitude' => 72.8777, 'featured' => 1,
    ],
    [
        'slug' => 'hawaii', 'name' => 'Hawaii', 'country' => 'United States', 'region' => 'North America',
        'summary' => 'Volcanoes, surf breaks and golden beaches in the middle of the Pacific.',
        'description' => "Snorkel with sea turtles at Hanauma Bay, hike Diamond Head at sunrise and learn to surf on Waikiki's gentle waves.\n\nIncludes a day trip to the Big Island to see the lava fields of Hawaiʻi Volcanoes National Park.",
        'image' => 'images/p-2.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 7, 'rating' => 4.0,
        'latitude' => 21.3069, 'longitude' => -157.8583, 'featured' => 1,
    ],
    [
        'slug' => 'sydney', 'name' => 'Sydney', 'country' => 'Australia', 'region' => 'Oceania',
        'summary' => 'Harbour views, the Opera House and the famous Bondi to Coogee coastal walk.',
        'description' => "Climb the Harbour Bridge, catch a performance at the Opera House and ferry across to Manly.\n\nThe package includes the Bondi to Coogee walk and a day in the Blue Mountains.",
        'image' => 'images/p-3.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 6, 'rating' => 4.0,
        'latitude' => -33.8688, 'longitude' => 151.2093, 'featured' => 1,
    ],
    [
        'slug' => 'paris', 'name' => 'Paris', 'country' => 'France', 'region' => 'Europe',
        'summary' => 'Cafés, world-class museums and evening walks along the Seine.',
        'description' => "See the Mona Lisa at the Louvre, picnic beneath the Eiffel Tower and wander the cobbled streets of Montmartre.\n\nIncludes skip-the-line museum passes and a Seine river cruise at dusk.",
        'image' => 'images/p-4.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 5, 'rating' => 4.0,
        'latitude' => 48.8566, 'longitude' => 2.3522, 'featured' => 1,
    ],
    [
        'slug' => 'tokyo', 'name' => 'Tokyo', 'country' => 'Japan', 'region' => 'Asia',
        'summary' => 'Neon nights, ancient temples and the world\'s best sushi.',
        'description' => "Cross the Shibuya scramble, visit Sensō-ji temple in Asakusa and explore the backstreets of Shimokitazawa.\n\nIncludes a day trip to Mount Fuji and a sushi-making class.",
        'image' => 'images/p-5.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 7, 'rating' => 4.0,
        'latitude' => 35.6762, 'longitude' => 139.6503, 'featured' => 1,
    ],
    [
        'slug' => 'egypt', 'name' => 'Egypt', 'country' => 'Egypt', 'region' => 'Africa',
        'summary' => 'The Pyramids of Giza, Nile cruises and the treasures of the pharaohs.',
        'description' => "Stand before the Great Pyramid and the Sphinx, explore Cairo's Khan el-Khalili bazaar and see Tutankhamun's treasures.\n\nIncludes a felucca sail on the Nile at sunset.",
        'image' => 'images/p-6.jpg', 'price' => 90, 'old_price' => 120, 'duration_days' => 6, 'rating' => 4.0,
        'latitude' => 30.0444, 'longitude' => 31.2357, 'featured' => 1,
    ],
    [
        'slug' => 'bali', 'name' => 'Bali', 'country' => 'Indonesia', 'region' => 'Asia',
        'summary' => 'Temple gates, rice terraces and laid-back beach towns.',
        'description' => "Photograph the Handara Gate, walk the Tegallalang rice terraces near Ubud and watch a kecak fire dance at Uluwatu.\n\nIncludes a cooking class and a sunrise trek up Mount Batur.",
        'image' => 'images/g-4.jpg', 'price' => 140, 'old_price' => 180, 'duration_days' => 8, 'rating' => 4.8,
        'latitude' => -8.4095, 'longitude' => 115.1889, 'featured' => 0,
    ],
    [
        'slug' => 'athens', 'name' => 'Athens', 'country' => 'Greece', 'region' => 'Europe',
        'summary' => 'The Acropolis, the Plaka and island-hopping from Piraeus.',
        'description' => "Climb to the Parthenon, browse the Acropolis Museum and eat souvlaki in the Plaka.\n\nIncludes a day cruise to the Saronic islands of Hydra, Poros and Aegina.",
        'image' => 'images/g-3.jpg', 'price' => 120, 'old_price' => null, 'duration_days' => 4, 'rating' => 4.5,
        'latitude' => 37.9715, 'longitude' => 23.7257, 'featured' => 0,
    ],
    [
        'slug' => 'pisa', 'name' => 'Pisa', 'country' => 'Italy', 'region' => 'Europe',
        'summary' => 'The Leaning Tower, Tuscan countryside and day trips to Florence.',
        'description' => "Climb the Leaning Tower in the Piazza dei Miracoli, then head into Tuscany for vineyards and hill towns.\n\nIncludes a train day trip to Florence and a wine tasting in Chianti.",
        'image' => 'images/g-5.jpg', 'price' => 75, 'old_price' => 95, 'duration_days' => 3, 'rating' => 4.2,
        'latitude' => 43.7230, 'longitude' => 10.3966, 'featured' => 0,
    ],
    [
        'slug' => 'phi-phi-islands', 'name' => 'Phi Phi Islands', 'country' => 'Thailand', 'region' => 'Asia',
        'summary' => 'Limestone cliffs, longtail boats and turquoise lagoons.',
        'description' => "Take a longtail boat to Maya Bay, snorkel in Pileh Lagoon and watch the sunset from the Phi Phi viewpoint.\n\nIncludes island-hopping tours and two nights on Koh Phi Phi Don.",
        'image' => 'images/g-2.jpg', 'price' => 110, 'old_price' => 150, 'duration_days' => 5, 'rating' => 4.7,
        'latitude' => 7.7407, 'longitude' => 98.7784, 'featured' => 0,
    ],
    [
        'slug' => 'koh-tao', 'name' => 'Koh Tao', 'country' => 'Thailand', 'region' => 'Asia',
        'summary' => 'Coral reefs, dive schools and quiet viewpoints over the Gulf of Thailand.',
        'description' => "Get your open-water dive certificate, hike to the John-Suwan viewpoint and swim at Koh Nang Yuan's sandbar.\n\nIncludes a snorkelling tour around the island.",
        'image' => 'images/g-1.jpg', 'price' => 65, 'old_price' => null, 'duration_days' => 4, 'rating' => 4.6,
        'latitude' => 10.0956, 'longitude' => 99.8404, 'featured' => 0,
    ],
];
