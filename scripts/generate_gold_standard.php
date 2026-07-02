<?php

$dir = "d:/Antarman/code/palm-reading/database/knowledge/rules/palm/life_line/long";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$sections = [
    "personality" => ["Personality", "व्यक्तित्व", "a long Life Line generally reflects resilience, vitality, and steady life energy. This suggests a grounded character.", "लंबी और स्पष्ट जीवन रेखा अच्छे स्वास्थ्य, जीवन ऊर्जा और कठिन परिस्थितियों से उबरने की क्षमता का संकेत मानी जाती है।"],
    "health_and_vitality" => ["Health & Vitality", "स्वास्थ्य और जीवन शक्ति", "a long Life Line is generally associated with strong vitality and physical endurance. It reflects a robust constitution.", "लंबी जीवन रेखा आम तौर पर मजबूत शारीरिक जीवन शक्ति और सहनशक्ति से जुड़ी होती है।"],
    "career" => ["Career", "करियर", "a long Life Line indicates a steady approach to career progression, suggesting patience and long-term stamina in professional pursuits.", "लंबी जीवन रेखा करियर में स्थिर प्रगति, धैर्य और दीर्घकालिक सहनशक्ति का संकेत देती है।"],
    "business" => ["Business", "व्यापार", "a deep and long Life Line reflects the sustained energy and resilience required for long-term entrepreneurial success.", "गहरी और लंबी जीवन रेखा दीर्घकालिक व्यावसायिक सफलता के लिए आवश्यक निरंतर ऊर्जा को दर्शाती है।"],
    "wealth" => ["Wealth", "धन", "a long Life Line suggests steady accumulation of resources over time, driven by consistent effort and stamina.", "यह रेखा समय के साथ निरंतर प्रयास और ऊर्जा द्वारा धन संचय का संकेत देती है।"],
    "love" => ["Love", "प्रेम", "a long Life Line suggests a grounded and steady approach to romance, with the stamina to nurture long-lasting bonds.", "लंबी जीवन रेखा प्रेम संबंधों में एक स्थिर और धैर्यवान दृष्टिकोण का संकेत देती है।"],
    "marriage" => ["Marriage", "विवाह", "a continuous Life Line often reflects the stability and resilience beneficial for a lasting and supportive marital life.", "निरंतर जीवन रेखा अक्सर स्थायी और सहायक वैवाहिक जीवन के लिए लाभकारी स्थिरता को दर्शाती है।"],
    "family" => ["Family", "परिवार", "a strong Life Line indicates a deep-rooted sense of family connection and the vitality to support loved ones.", "एक मजबूत जीवन रेखा पारिवारिक जुड़ाव और प्रियजनों का समर्थन करने की क्षमता का संकेत देती है।"],
    "education" => ["Education", "शिक्षा", "a long Life Line suggests the mental stamina and patience required for continuous learning and long-term academic pursuits.", "लंबी जीवन रेखा निरंतर सीखने और दीर्घकालिक शैक्षणिक गतिविधियों के लिए आवश्यक धैर्य का संकेत देती है।"],
    "leadership" => ["Leadership", "नेतृत्व", "a long Life Line reflects the enduring energy and resilience often found in steady, reliable leaders.", "लंबी जीवन रेखा स्थिर और विश्वसनीय नेताओं में पाई जाने वाली स्थायी ऊर्जा को दर्शाती है।"],
    "travel" => ["Travel", "यात्रा", "a long Life Line indicates the physical vitality and stamina to enjoy extended journeys and worldly exploration.", "लंबी जीवन रेखा विस्तारित यात्राओं का आनंद लेने के लिए शारीरिक जीवन शक्ति का संकेत देती है।"],
    "spirituality" => ["Spirituality", "आध्यात्मिकता", "a long Life Line suggests a grounded energy that supports steady and lifelong spiritual growth.", "लंबी जीवन रेखा एक ऐसी स्थिर ऊर्जा का संकेत देती है जो आजीवन आध्यात्मिक विकास का समर्थन करती है।"],
    "strengths" => ["Strengths", "ताकत", "a long Life Line highlights your natural resilience, steady energy, and ability to endure life's challenges.", "लंबी जीवन रेखा आपके प्राकृतिक लचीलेपन, स्थिर ऊर्जा और जीवन की चुनौतियों को सहने की क्षमता को उजागर करती है।"],
    "challenges" => ["Challenges", "चुनौतियां", "a long Life Line suggests you may sometimes overextend yourself, requiring you to balance your abundant energy with rest.", "लंबी जीवन रेखा बताती है कि आप कभी-कभी अपनी क्षमता से अधिक कार्य कर सकते हैं, इसलिए विश्राम आवश्यक है।"],
    "lucky_influences" => ["Lucky Influences", "भाग्यशाली प्रभाव", "a strong Life Line is often considered a lucky marker of overall stability and enduring positive energy.", "एक मजबूत जीवन रेखा को अक्सर समग्र स्थिरता और स्थायी सकारात्मक ऊर्जा का भाग्यशाली प्रतीक माना जाता है।"],
    "remedies" => ["Remedies", "उपाय", "a long Life Line indicates strong baseline energy, but maintaining a balanced routine is essential to protect this vitality.", "लंबी जीवन रेखा मजबूत आधारभूत ऊर्जा का संकेत देती है, लेकिन इसे बनाए रखने के लिए संतुलित दिनचर्या आवश्यक है।"]
];

$intro_en = "According to traditional palmistry, ";
$intro_hi = "पारंपरिक हस्तरेखा शास्त्र के अनुसार, ";
$outro_en = " Additional markings on your palm may reveal important life phases and hidden influences explored in your detailed report.";
$outro_hi = " यह केवल प्रारंभिक संकेत है। आपकी हथेली में मौजूद अन्य रेखाएँ जीवन के महत्वपूर्ण चरणों के बारे में अधिक जानकारी प्रदान कर सकती हैं।";

foreach ($sections as $key => $data) {
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $rule_uid = strtoupper("PALM_LIFE_LONG_" . $key);
    
    $json = [
        "metadata" => [
            "version" => "1.0",
            "created" => date("Y-m-d"),
            "modified" => date("Y-m-d"),
            "author" => "MeraRashifal",
            "status" => "draft"
        ],
        "rules" => [
            [
                "uuid" => $uuid,
                "rule_uid" => $rule_uid,
                "analysis" => "palm",
                "feature" => "life_line",
                "value" => "long",
                "section" => $key,
                "priority" => 1000,
                "weight" => 100,
                "importance" => "High",
                "minimum_confidence" => 0.80,
                "visibility" => "free",
                "rule_type" => "prediction",
                "tone" => "positive",
                "applicability" => "all",
                "source" => "TIP001",
                "keywords" => [$key, "life line", "long", "vitality"],
                "translations" => [
                    "en" => $intro_en . $data[2] . $outro_en,
                    "hi" => $intro_hi . $data[3] . $outro_hi
                ]
            ]
        ]
    ];
    
    $file_path = $dir . "/" . $key . ".json";
    file_put_contents($file_path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "Successfully generated 16 rules.";

?>
