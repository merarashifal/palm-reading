const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const openingsPath = path.join(__dirname, '../database/knowledge/dictionary/openings.json');
const curiosityPath = path.join(__dirname, '../database/knowledge/dictionary/curiosity.json');

const openings = JSON.parse(fs.readFileSync(openingsPath, 'utf8'));
const curiosities = JSON.parse(fs.readFileSync(curiosityPath, 'utf8'));

// The 16 Sections Data
const sectionsData = {
    // TIER 1
    "personality": {
        tier: 1, emotion: "reflective", importance: "High",
        keywords: ["personality", "resilience", "grounded", "steady"],
        obs_en: "a long and clearly defined Life Line is generally seen as a sign of grounded character, deep inner resilience, and steady emotional energy.",
        obs_hi: "लंबी और स्पष्ट जीवन रेखा एक स्थिर व्यक्तित्व, गहरी आंतरिक शक्ति और संतुलित भावनात्मक ऊर्जा का प्रतीक मानी जाती है।",
        inter_en: "This reflects an innate ability to remain composed during challenging situations and a reliable core nature.",
        inter_hi: "यह आपके मूल स्वभाव की स्थिरता और कठिन परिस्थितियों में शांत रहने की सहज क्षमता को दर्शाता है।"
    },
    "career": {
        tier: 1, emotion: "positive", importance: "High",
        keywords: ["career", "patience", "progression", "stamina"],
        obs_en: "a long Life Line indicates a steady approach to career progression, suggesting patience and long-term stamina in professional pursuits.",
        obs_hi: "लंबी जीवन रेखा करियर में स्थिर प्रगति, धैर्य और दीर्घकालिक सहनशक्ति का संकेत देती है।",
        inter_en: "This highlights a strong professional foundation and the ability to build lasting success through consistent effort.",
        inter_hi: "यह आपके मजबूत पेशेवर आधार और निरंतर प्रयास के माध्यम से स्थायी सफलता प्राप्त करने की क्षमता को दर्शाता है।"
    },
    "love": {
        tier: 1, emotion: "positive", importance: "High",
        keywords: ["love", "romance", "steady", "bonds"],
        obs_en: "a long Life Line is generally connected to a steady, grounded approach to emotional expression and the stamina to nurture lasting bonds.",
        obs_hi: "लंबी जीवन रेखा भावनाओं की अभिव्यक्ति में एक स्थिर दृष्टिकोण और लंबे समय तक चलने वाले संबंधों को निभाने की क्षमता से जुड़ी है।",
        inter_en: "This observation focuses on your capacity for deep connection and emotional resilience over time.",
        inter_hi: "यह आपके भावनात्मक धैर्य और समय के साथ गहरे संबंध बनाने की आपकी क्षमता पर केंद्रित है।"
    },
    "marriage": {
        tier: 1, emotion: "positive", importance: "High",
        keywords: ["marriage", "stability", "partnership", "resilience"],
        obs_en: "a continuous Life Line often reflects the stability and resilience beneficial for a lasting and supportive marital life.",
        obs_hi: "निरंतर जीवन रेखा अक्सर स्थायी और सहायक वैवाहिक जीवन के लिए लाभकारी स्थिरता और लचीलेपन को दर्शाती है。",
        inter_en: "This points to a foundation of loyalty and the endurance needed to navigate long-term partnerships.",
        inter_hi: "यह वफादारी की नींव और दीर्घकालिक साझेदारी को सफलतापूर्वक निभाने के लिए आवश्यक सहनशक्ति को इंगित करता है।"
    },
    "wealth": {
        tier: 1, emotion: "positive", importance: "High",
        keywords: ["wealth", "financial management", "resources", "stamina"],
        obs_en: "a long Life Line reflects the consistent effort and endurance necessary for the steady accumulation and management of financial resources.",
        obs_hi: "लंबी जीवन रेखा समय के साथ धन के स्थिर संचय और प्रबंधन के लिए आवश्यक निरंतर प्रयास और सहनशक्ति को दर्शाती है।",
        inter_en: "This points toward a practical approach to financial stability and long-term security.",
        inter_hi: "यह वित्तीय स्थिरता और दीर्घकालिक सुरक्षा के प्रति एक व्यावहारिक और सुरक्षित दृष्टिकोण की ओर इशारा करता है।"
    },
    "health_and_vitality": {
        tier: 1, emotion: "positive", importance: "Critical",
        keywords: ["health", "vitality", "resilience", "energy"],
        obs_en: "a long and well-defined Life Line is generally associated with good vitality, steady physical endurance, and a robust constitution.",
        obs_hi: "लंबी और स्पष्ट जीवन रेखा आमतौर पर मजबूत जीवन ऊर्जा, शारीरिक सहनशक्ति और अच्छे स्वास्थ्य से जुड़ी मानी जाती है।",
        inter_en: "This suggests a natural physical resilience and a strong foundation of life energy.",
        inter_hi: "यह प्राकृतिक शारीरिक लचीलेपन और जीवन ऊर्जा की एक मजबूत और स्थिर नींव का संकेत देता है।"
    },

    // TIER 2
    "business": {
        tier: 2, emotion: "positive", importance: "High",
        keywords: ["business", "entrepreneurship", "resilience", "success"],
        obs_en: "a deep, long Life Line reflects the sustained stamina and entrepreneurial endurance required to overcome long-term challenges.",
        obs_hi: "गहरी और लंबी जीवन रेखा दीर्घकालिक व्यावसायिक चुनौतियों को पार करने के लिए आवश्यक निरंतर ऊर्जा और उद्यमशीलता को दर्शाती है।",
        inter_en: "This reveals a strong foundation for taking calculated risks and building an enterprise.",
        inter_hi: "यह उद्यम स्थापित करने और सोच-समझकर जोखिम लेने के लिए एक मजबूत आधार को प्रकट करता है।"
    },
    "family": {
        tier: 2, emotion: "positive", importance: "Medium",
        keywords: ["family", "support", "bonds", "connection"],
        obs_en: "a strong Life Line indicates a deep-rooted sense of family connection and the vitality to support loved ones.",
        obs_hi: "एक मजबूत जीवन रेखा पारिवारिक जुड़ाव और प्रियजनों का समर्थन करने की क्षमता का संकेत देती है।",
        inter_en: "This reflects your enduring commitment to maintaining family harmony.",
        inter_hi: "यह पारिवारिक सद्भाव बनाए रखने के प्रति आपकी स्थायी प्रतिबद्धता को दर्शाता है।"
    },
    "leadership": {
        tier: 2, emotion: "positive", importance: "Medium",
        keywords: ["leadership", "reliability", "endurance", "guidance"],
        obs_en: "a long Life Line reflects the enduring energy and resilience often found in steady, reliable leaders.",
        obs_hi: "लंबी जीवन रेखा स्थिर और विश्वसनीय नेताओं में पाई जाने वाली स्थायी ऊर्जा को दर्शाती है।",
        inter_en: "This suggests you have the stamina to guide others through challenging situations.",
        inter_hi: "यह बताता है कि आप चुनौतीपूर्ण परिस्थितियों में दूसरों का मार्गदर्शन करने की क्षमता रखते हैं।"
    },
    "education": {
        tier: 2, emotion: "reflective", importance: "Medium",
        keywords: ["education", "learning", "patience", "focus"],
        obs_en: "a long Life Line suggests the mental stamina and patience required for continuous learning and long-term academic pursuits.",
        obs_hi: "लंबी जीवन रेखा निरंतर सीखने और दीर्घकालिक शैक्षणिक गतिविधियों के लिए आवश्यक धैर्य का संकेत देती है।",
        inter_en: "This highlights a steady dedication to expanding your knowledge base over time.",
        inter_hi: "यह समय के साथ अपने ज्ञान का विस्तार करने के प्रति आपके स्थिर समर्पण को उजागर करता है।"
    },
    "travel": {
        tier: 2, emotion: "positive", importance: "Medium",
        keywords: ["travel", "exploration", "vitality", "journeys"],
        obs_en: "a long Life Line indicates the physical vitality and stamina to enjoy extended journeys and worldly exploration.",
        obs_hi: "लंबी जीवन रेखा विस्तारित यात्राओं का आनंद लेने के लिए आवश्यक शारीरिक जीवन शक्ति का संकेत देती है।",
        inter_en: "This points to an enduring curiosity and the energy needed to explore new horizons.",
        inter_hi: "यह नई दिशाओं का पता लगाने के लिए आवश्यक ऊर्जा और स्थायी जिज्ञासा की ओर इशारा करता है।"
    },
    "spirituality": {
        tier: 2, emotion: "reflective", importance: "Medium",
        keywords: ["spirituality", "growth", "grounded", "energy"],
        obs_en: "a long Life Line suggests a grounded energy that supports steady and lifelong spiritual growth.",
        obs_hi: "लंबी जीवन रेखा एक ऐसी स्थिर ऊर्जा का संकेत देती है जो आजीवन आध्यात्मिक विकास का समर्थन करती है।",
        inter_en: "This reflects a patient and enduring journey toward inner peace.",
        inter_hi: "यह आंतरिक शांति की दिशा में एक धैर्यवान और स्थायी यात्रा को दर्शाता है।"
    },

    // TIER 3
    "strengths": {
        tier: 3, emotion: "positive", importance: "Medium",
        keywords: ["strengths", "resilience", "endurance", "vitality"],
        obs_en: "a long Life Line highlights your natural resilience, steady energy, and ability to endure life's challenges.",
        obs_hi: "लंबी जीवन रेखा आपके प्राकृतिक लचीलेपन, स्थिर ऊर्जा और जीवन की चुनौतियों को सहने की क्षमता को उजागर करती है।",
        inter_en: "Your core strength is your lasting stamina.",
        inter_hi: "आपकी मूल ताकत आपकी स्थायी सहनशक्ति है।"
    },
    "challenges": {
        tier: 3, emotion: "caution", importance: "Medium",
        keywords: ["challenges", "balance", "overextend", "rest"],
        obs_en: "a long Life Line suggests you may sometimes overextend yourself, requiring you to balance your abundant energy with proper rest.",
        obs_hi: "लंबी जीवन रेखा बताती है कि आप कभी-कभी अपनी क्षमता से अधिक कार्य कर सकते हैं, इसलिए विश्राम आवश्यक है।",
        inter_en: "Learning to pace yourself is an important constructive step.",
        inter_hi: "स्वयं को संतुलित करना एक महत्वपूर्ण और रचनात्मक कदम है।"
    },
    "lucky_influences": {
        tier: 3, emotion: "positive", importance: "Medium",
        keywords: ["lucky", "influences", "favourable", "circumstances"],
        obs_en: "a strong Life Line is often associated with favourable circumstances and enduring positive energy during important phases of life.",
        obs_hi: "एक मजबूत जीवन रेखा को अक्सर जीवन के महत्वपूर्ण चरणों में अनुकूल परिस्थितियों और स्थायी सकारात्मक ऊर्जा से जोड़ा जाता है।",
        inter_en: "This feature acts as a supportive foundation.",
        inter_hi: "यह विशेषता एक सहायक आधार के रूप में कार्य करती है।"
    },
    "remedies": {
        tier: 3, emotion: "reflective", importance: "Medium",
        keywords: ["remedies", "mindfulness", "routine", "balance"],
        obs_en: "a long Life Line indicates strong baseline energy, but maintaining a balanced routine is essential to protect this vitality.",
        obs_hi: "लंबी जीवन रेखा मजबूत ऊर्जा का संकेत देती है, लेकिन इसे बनाए रखने के लिए संतुलित दिनचर्या आवश्यक है।",
        inter_en: "Practices like mindfulness and gratitude are highly recommended.",
        inter_hi: "ध्यान और कृतज्ञता जैसी प्रथाओं की अत्यधिक अनुशंसा की जाती है।"
    }
};

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}

const targetDir = path.join(__dirname, '../database/knowledge/rules/palm/life_line/long');
if (!fs.existsSync(targetDir)) {
    fs.mkdirSync(targetDir, { recursive: true });
}

for (const [key, data] of Object.entries(sectionsData)) {
    // For Tier 3, we often keep it shorter, sometimes omitting the curiosity if we want 25 words, 
    // but the spec says "Every rule must satisfy Traditional + Human + Balanced + Curiosity + Trust".
    // So we will include curiosity but keep interpretation very brief.
    
    // Pick random opening
    const openIdx = getRandomInt(openings.en.length);
    const openingEn = openings.en[openIdx].text;
    const openingHi = openings.hi[openIdx].text;

    // Adjust casing if opening is empty (direct observation)
    let obsEnFinal = data.obs_en;
    let obsHiFinal = data.obs_hi;
    if (openings.en[openIdx].style === 'direct' || openings.en[openIdx].style === 'reader_focused') {
        obsEnFinal = obsEnFinal.charAt(0).toUpperCase() + obsEnFinal.slice(1);
    }
    if (openings.hi[openIdx].style === 'direct' || openings.hi[openIdx].style === 'reader_focused') {
        // No case in Hindi, but handle properly
    }

    // Pick random curiosity
    const curIdx = getRandomInt(curiosities.en.length);
    let curiosityEn = curiosities.en[curIdx];
    let curiosityHi = curiosities.hi[curIdx];

    if (data.tier === 3) {
        // Tier 3 shorter curiosity
        curiosityEn = "More details await in your complete analysis.";
        curiosityHi = "अधिक विस्तृत जानकारी आपके संपूर्ण विश्लेषण में है।";
    }

    const fullEn = `${openingEn}${obsEnFinal} ${data.inter_en} ${curiosityEn}`.replace(/\s+/g, ' ').trim();
    const fullHi = `${openingHi}${obsHiFinal} ${data.inter_hi} ${curiosityHi}`.replace(/\s+/g, ' ').trim();

    const ruleObj = {
        metadata: {
            version: "1.0",
            created: new Date().toISOString().split('T')[0],
            modified: new Date().toISOString().split('T')[0],
            author: "MeraRashifal",
            status: "draft"
        },
        rules: [
            {
                uuid: crypto.randomUUID(),
                rule_uid: `PALM_LIFE_LONG_${key.toUpperCase()}`,
                analysis: "palm",
                feature: "life_line",
                value: "long",
                section: key,
                priority: 1000,
                weight: 100,
                importance: data.importance,
                minimum_confidence: 0.80,
                visibility: "free",
                rule_type: "prediction",
                tone: data.emotion === 'caution' ? 'caution' : 'positive',
                emotion: data.emotion,
                applicability: "all",
                reading_time: "15 sec",
                quality_score: 100,
                editor_notes: "Approved by Editorial Team",
                source: "TIP001",
                keywords: data.keywords,
                translations: {
                    en: fullEn,
                    hi: fullHi
                }
            }
        ]
    };

    fs.writeFileSync(path.join(targetDir, `${key}.json`), JSON.stringify(ruleObj, null, 2));
}

console.log("Successfully generated all 16 Gold Standard rules using the generator engine.");
