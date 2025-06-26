// Global variables
let currentStep = 1;
let currentQuestion = 0;
let map = null;
let autocomplete = null;
let quizQuestions = [];
let appSettings = {};
let formData = {
    address: '',
    coordinates: null,
    name: '',
    email: '',
    phone: '',
    propertyType: '',
    bedrooms: '',
    bathrooms: '',
    priceRange: '',
    timeline: '',
    financing: ''
};

// Load content from database
async function loadContent() {
    try {
        const response = await fetch('get_content.php');
        const data = await response.json();
        
        if (data.success) {
            appSettings = data.settings;
            quizQuestions = data.questions;
            updatePageContent();
        } else {
            console.error('Failed to load content:', data.message);
            // Fallback to default content
            loadDefaultContent();
        }
    } catch (error) {
        console.error('Error loading content:', error);
        // Fallback to default content
        loadDefaultContent();
    }
}

// Fallback default content
function loadDefaultContent() {
    // ... keep existing code (default quiz questions and settings)
    quizQuestions = [
        {
            id: 'propertyType',
            title: 'What type of property are you looking for?',
            icon: 'fas fa-home',
            options: [
                { option_value: 'single-family', option_label: 'Single Family Home' },
                { option_value: 'condo', option_label: 'Condominium' },
                { option_value: 'townhouse', option_label: 'Townhouse' },
                { option_value: 'multi-family', option_label: 'Multi-Family' }
            ]
        },
        {
            id: 'bedrooms',
            title: 'How many bedrooms do you need?',
            icon: 'fas fa-bed',
            options: [
                { option_value: '1', option_label: '1 Bedroom' },
                { option_value: '2', option_label: '2 Bedrooms' },
                { option_value: '3', option_label: '3 Bedrooms' },
                { option_value: '4+', option_label: '4+ Bedrooms' }
            ]
        },
        {
            id: 'bathrooms',
            title: 'How many bathrooms do you prefer?',
            icon: 'fas fa-bath',
            options: [
                { option_value: '1', option_label: '1 Bathroom' },
                { option_value: '1.5', option_label: '1.5 Bathrooms' },
                { option_value: '2', option_label: '2 Bathrooms' },
                { option_value: '3+', option_label: '3+ Bathrooms' }
            ]
        },
        {
            id: 'priceRange',
            title: 'What is your budget range?',
            icon: 'fas fa-dollar-sign',
            options: [
                { option_value: 'under-300k', option_label: 'Under $300,000' },
                { option_value: '300k-500k', option_label: '$300,000 - $500,000' },
                { option_value: '500k-750k', option_label: '$500,000 - $750,000' },
                { option_value: 'over-750k', option_label: 'Over $750,000' }
            ]
        },
        {
            id: 'timeline',
            title: 'When are you looking to buy?',
            icon: 'fas fa-calendar',
            options: [
                { option_value: 'immediately', option_label: 'Immediately' },
                { option_value: '1-3-months', option_label: '1-3 Months' },
                { option_value: '3-6-months', option_label: '3-6 Months' },
                { option_value: '6-months-plus', option_label: '6+ Months' }
            ]
        },
        {
            id: 'financing',
            title: 'How will you finance your purchase?',
            icon: 'fas fa-credit-card',
            options: [
                { option_value: 'mortgage', option_label: 'Mortgage/Loan' },
                { option_value: 'cash', option_label: 'Cash Purchase' },
                { option_value: 'pre-approved', option_label: 'Pre-approved' },
                { option_value: 'need-help', option_label: 'Need Help with Financing' }
            ]
        }
    ];
    
    appSettings = {
        site_title: 'Find Your Dream Home',
        site_subtitle: 'Enter your desired location to get started',
        background_image: 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
        step1_search_placeholder: 'Enter address, city, or ZIP code',
        step1_start_button_text: 'Start Your Search',
        step1_powered_by_text: 'Powered by Google Maps • Trusted by thousands of buyers'
    };
}

// Update page content with loaded settings
function updatePageContent() {
    // Update background image
    if (appSettings.background_image) {
        document.body.style.backgroundImage = `url('${appSettings.background_image}')`;
    }
    
    // Update step 1 content
    if (appSettings.site_title) {
        const titleElement = document.querySelector('#step1 h1');
        if (titleElement) titleElement.textContent = appSettings.site_title;
    }
    
    if (appSettings.site_subtitle) {
        const subtitleElement = document.querySelector('#step1 p');
        if (subtitleElement) subtitleElement.textContent = appSettings.site_subtitle;
    }
    
    if (appSettings.step1_search_placeholder) {
        const inputElement = document.getElementById('addressInput');
        if (inputElement) inputElement.placeholder = appSettings.step1_search_placeholder;
    }
    
    if (appSettings.step1_start_button_text) {
        const buttonElement = document.getElementById('startSearchBtn');
        if (buttonElement) buttonElement.textContent = appSettings.step1_start_button_text;
    }
    
    if (appSettings.step1_powered_by_text) {
        const poweredByElement = document.querySelector('.powered-by');
        if (poweredByElement) poweredByElement.textContent = appSettings.step1_powered_by_text;
    }
    
    // Update step 3 content
    if (appSettings.step3_title) {
        const titleElement = document.querySelector('#step3 h2');
        if (titleElement) titleElement.textContent = appSettings.step3_title;
    }
    
    if (appSettings.step3_subtitle) {
        const subtitleElement = document.querySelector('#step3 .form-header p');
        if (subtitleElement) subtitleElement.textContent = appSettings.step3_subtitle;
    }
    
    // Update form labels and placeholders
    updateFormField('fullName', 'step3_name_label', 'step3_name_placeholder');
    updateFormField('email', 'step3_email_label', 'step3_email_placeholder');
    updateFormField('phone', 'step3_phone_label', 'step3_phone_placeholder');
    
    // Update privacy text
    if (appSettings.privacy_text || appSettings.step3_privacy_title) {
        const privacyTitle = document.querySelector('.privacy-notice h4');
        const privacyText = document.querySelector('.privacy-notice p');
        
        if (privacyTitle && appSettings.step3_privacy_title) {
            privacyTitle.textContent = appSettings.step3_privacy_title;
        }
        if (privacyText && appSettings.privacy_text) {
            privacyText.textContent = appSettings.privacy_text;
        }
    }
    
    // Update step 4 content
    if (appSettings.step4_title) {
        const titleElement = document.querySelector('#step4 h2');
        if (titleElement) titleElement.textContent = appSettings.step4_title;
    }
    
    if (appSettings.step4_preferences_title) {
        const preferencesTitle = document.querySelector('#step4 h3');
        if (preferencesTitle) preferencesTitle.textContent = appSettings.step4_preferences_title;
    }
}

function updateFormField(fieldId, labelKey, placeholderKey) {
    const field = document.getElementById(fieldId);
    const label = document.querySelector(`label[for="${fieldId}"]`);
    
    if (label && appSettings[labelKey]) {
        label.textContent = appSettings[labelKey];
    }
    if (field && appSettings[placeholderKey]) {
        field.placeholder = appSettings[placeholderKey];
    }
}

// Initialize the application
async function initializeApp() {
    await loadContent();
    initializeAddressSearch();
    setupEventListeners();
    showStep(1);
}

// Initialize Google Maps autocomplete
function initializeAddressSearch() {
    const addressInput = document.getElementById('addressInput');
    
    if (window.google && addressInput) {
        const apiKey = appSettings.google_maps_api_key || 'YOUR_GOOGLE_MAPS_API_KEY';
        
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['address'],
            componentRestrictions: { country: 'us' }
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                formData.address = place.formatted_address;
                formData.coordinates = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };
                enableStartButton();
            }
        });
    }
}

// Setup all event listeners
function setupEventListeners() {
    // Address input
    const addressInput = document.getElementById('addressInput');
    addressInput.addEventListener('input', function() {
        const startBtn = document.getElementById('startSearchBtn');
        if (this.value.trim()) {
            startBtn.disabled = false;
        } else {
            startBtn.disabled = true;
        }
    });

    // Start search button
    document.getElementById('startSearchBtn').addEventListener('click', function() {
        if (formData.address || document.getElementById('addressInput').value.trim()) {
            if (!formData.address) {
                formData.address = document.getElementById('addressInput').value.trim();
            }
            showStep(2);
            initializeQuiz();
        }
    });

    // Quiz navigation
    document.getElementById('quizBackBtn').addEventListener('click', function() {
        if (currentQuestion > 0) {
            currentQuestion--;
            displayQuestion();
        } else {
            showStep(1);
        }
    });

    document.getElementById('quizNextBtn').addEventListener('click', function() {
        if (currentQuestion < quizQuestions.length - 1) {
            currentQuestion++;
            displayQuestion();
        } else {
            showStep(3);
        }
    });

    // Form navigation
    document.getElementById('formBackBtn').addEventListener('click', function() {
        showStep(2);
    });

    document.getElementById('formNextBtn').addEventListener('click', function() {
        if (validateForm()) {
            showStep(4);
            initializeMap();
            displayPreferences();
        }
    });

    // Results navigation
    document.getElementById('resultsBackBtn').addEventListener('click', function() {
        showStep(3);
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        submitForm();
    });

    // Form inputs
    setupFormValidation();
}

// Enable start button
function enableStartButton() {
    document.getElementById('startSearchBtn').disabled = false;
}

// Show specific step
function showStep(stepNumber) {
    // Hide all steps
    const steps = document.querySelectorAll('.step');
    steps.forEach(step => {
        step.classList.remove('active');
    });

    // Show current step
    const currentStepElement = document.getElementById(`step${stepNumber}`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
        currentStepElement.classList.add('fade-in');
    }

    currentStep = stepNumber;
}

// Initialize quiz
function initializeQuiz() {
    currentQuestion = 0;
    displayQuestion();
}

// Display current quiz question
function displayQuestion() {
    const question = quizQuestions[currentQuestion];
    
    // Update question content
    document.getElementById('questionIcon').className = question.icon;
    document.getElementById('questionTitle').textContent = question.title;
    
    // Update progress
    const progress = ((currentQuestion + 1) / quizQuestions.length) * 100;
    document.getElementById('progressFill').style.width = `${progress}%`;
    document.getElementById('progressText').textContent = `Question ${currentQuestion + 1} of ${quizQuestions.length}`;
    
    // Create options
    const optionsContainer = document.getElementById('questionOptions');
    optionsContainer.innerHTML = '';
    
    question.options.forEach(option => {
        const optionElement = document.createElement('div');
        optionElement.className = 'option';
        optionElement.innerHTML = `
            <input type="radio" name="question${currentQuestion}" value="${option.option_value}" id="${option.option_value}">
            <label for="${option.option_value}">${option.option_label}</label>
        `;
        
        optionElement.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            updateQuestionSelection();
        });
        
        optionsContainer.appendChild(optionElement);
    });
    
    // Check if we have a previous answer
    const previousAnswer = formData[question.id];
    if (previousAnswer) {
        const radio = document.querySelector(`input[value="${previousAnswer}"]`);
        if (radio) {
            radio.checked = true;
            updateQuestionSelection();
        }
    } else {
        document.getElementById('quizNextBtn').disabled = true;
    }
    
    // Update button text from settings
    const backBtn = document.getElementById('quizBackBtn');
    const nextBtn = document.getElementById('quizNextBtn');
    
    if (currentQuestion === 0) {
        backBtn.innerHTML = `<i class="fas fa-chevron-left"></i> ${appSettings.step2_back_button_text || 'Back'}`;
    } else {
        backBtn.innerHTML = `<i class="fas fa-chevron-left"></i> ${appSettings.step2_back_button_text || 'Back'}`;
    }
    
    if (currentQuestion === quizQuestions.length - 1) {
        nextBtn.innerHTML = `${appSettings.step2_continue_button_text || 'Continue'} <i class="fas fa-chevron-right"></i>`;
    } else {
        nextBtn.innerHTML = `${appSettings.step2_next_button_text || 'Next'} <i class="fas fa-chevron-right"></i>`;
    }
}

// Update quiz selection
function updateQuestionSelection() {
    const question = quizQuestions[currentQuestion];
    const selectedOption = document.querySelector(`input[name="question${currentQuestion}"]:checked`);
    
    if (selectedOption) {
        formData[question.id] = selectedOption.value;
        document.getElementById('quizNextBtn').disabled = false;
        
        // Update visual selection
        document.querySelectorAll('.option').forEach(option => {
            option.classList.remove('selected');
        });
        selectedOption.closest('.option').classList.add('selected');
    }
}

// Setup form validation
function setupFormValidation() {
    const inputs = ['fullName', 'email', 'phone'];
    
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        input.addEventListener('input', function() {
            clearError(inputId);
            updateFormData();
        });
        
        input.addEventListener('blur', function() {
            validateField(inputId);
        });
    });
}

// Update form data
function updateFormData() {
    formData.name = document.getElementById('fullName').value;
    formData.email = document.getElementById('email').value;
    formData.phone = document.getElementById('phone').value;
}

// Validate individual field
function validateField(fieldId) {
    const input = document.getElementById(fieldId);
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    switch (fieldId) {
        case 'fullName':
            if (!value) {
                errorMessage = 'Name is required';
                isValid = false;
            }
            break;
        case 'email':
            if (!value) {
                errorMessage = 'Email is required';
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(value)) {
                errorMessage = 'Please enter a valid email';
                isValid = false;
            }
            break;
        case 'phone':
            if (!value) {
                errorMessage = 'Phone number is required';
                isValid = false;
            }
            break;
    }
    
    if (!isValid) {
        showError(fieldId, errorMessage);
    }
    
    return isValid;
}

// Validate entire form
function validateForm() {
    updateFormData();
    
    const fields = ['fullName', 'email', 'phone'];
    let isValid = true;
    
    fields.forEach(fieldId => {
        if (!validateField(fieldId)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Show field error
function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId.replace('full', '').toLowerCase() + 'Error');
    
    input.classList.add('error');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

// Clear field error
function clearError(fieldId) {
    const input = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId.replace('full', '').toLowerCase() + 'Error');
    
    input.classList.remove('error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

// Initialize map on results page
function initializeMap() {
    if (!window.google || !formData.coordinates) return;
    
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    
    map = new google.maps.Map(mapElement, {
        center: formData.coordinates,
        zoom: 15,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });
    
    // Add marker
    new google.maps.Marker({
        position: formData.coordinates,
        map: map,
        title: formData.address,
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="18" fill="#2563eb" stroke="white" stroke-width="4"/>
                    <path d="M20 10L20 30M10 20L30 20" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
        }
    });
}

// Display user preferences summary
function displayPreferences() {
    // Update subtitle with settings
    const subtitleElement = document.querySelector('#step4 .results-header p');
    if (subtitleElement && appSettings.step4_subtitle_prefix) {
        subtitleElement.innerHTML = `${appSettings.step4_subtitle_prefix} <span id="selectedAddress">${formData.address}</span>`;
    } else {
        document.getElementById('selectedAddress').textContent = formData.address;
    }
    
    const summaryContainer = document.getElementById('preferencesSummary');
    summaryContainer.innerHTML = '';
    
    const preferences = [
        { label: 'Property Type', value: getPropertyTypeLabel(formData.propertyType) },
        { label: 'Bedrooms', value: formData.bedrooms },
        { label: 'Bathrooms', value: formData.bathrooms },
        { label: 'Price Range', value: getPriceRangeLabel(formData.priceRange) },
        { label: 'Timeline', value: getTimelineLabel(formData.timeline) },
        { label: 'Financing', value: getFinancingLabel(formData.financing) }
    ];
    
    preferences.forEach(pref => {
        if (pref.value) {
            const prefElement = document.createElement('div');
            prefElement.className = 'preference-item';
            prefElement.innerHTML = `
                <span class="preference-label">${pref.label}:</span>
                <span class="preference-value">${pref.value}</span>
            `;
            summaryContainer.appendChild(prefElement);
        }
    });
}

// Helper functions to get option labels
function getPropertyTypeLabel(type) {
    const question = quizQuestions.find(q => q.id === 'propertyType');
    if (question) {
        const option = question.options.find(opt => opt.option_value === type);
        return option ? option.option_label : type;
    }
    return type;
}

function getPriceRangeLabel(range) {
    const question = quizQuestions.find(q => q.id === 'priceRange');
    if (question) {
        const option = question.options.find(opt => opt.option_value === range);
        return option ? option.option_label : range;
    }
    return range;
}

function getTimelineLabel(timeline) {
    const question = quizQuestions.find(q => q.id === 'timeline');
    if (question) {
        const option = question.options.find(opt => opt.option_value === timeline);
        return option ? option.option_label : timeline;
    }
    return timeline;
}

function getFinancingLabel(financing) {
    const question = quizQuestions.find(q => q.id === 'financing');
    if (question) {
        const option = question.options.find(opt => opt.option_value === financing);
        return option ? option.option_label : financing;
    }
    return financing;
}

// Submit form
function submitForm() {
    console.log('Final form data:', formData);
    
    // Submit to backend
    fetch('submit.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal with dynamic content
            document.getElementById('submittedName').textContent = formData.name;
            
            // Update success modal content from settings
            const modalTitle = document.querySelector('#successModal h2');
            if (modalTitle && appSettings.success_modal_title_prefix) {
                modalTitle.innerHTML = `${appSettings.success_modal_title_prefix} <span id="submittedName">${formData.name}</span>!`;
            }
            
            const modalSubtitle = document.querySelector('#successModal > .modal-content > p');
            if (modalSubtitle && appSettings.success_modal_subtitle) {
                modalSubtitle.textContent = appSettings.success_modal_subtitle;
            }
            
            // Update next steps
            if (appSettings.success_modal_next_steps_title) {
                const nextStepsTitle = document.querySelector('.next-steps h3');
                if (nextStepsTitle) nextStepsTitle.textContent = appSettings.success_modal_next_steps_title;
            }
            
            const nextStepTexts = [
                appSettings.success_modal_step1_text,
                appSettings.success_modal_step2_text,
                appSettings.success_modal_step3_text
            ];
            
            const nextStepElements = document.querySelectorAll('.next-step span');
            nextStepElements.forEach((element, index) => {
                if (nextStepTexts[index]) {
                    element.textContent = nextStepTexts[index];
                }
            });
            
            // Update button text
            const newSearchBtn = document.querySelector('#successModal .primary-btn');
            if (newSearchBtn && appSettings.success_modal_button_text) {
                newSearchBtn.textContent = appSettings.success_modal_button_text;
            }
            
            document.getElementById('successModal').classList.add('show');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('Error submitting form');
    });
}

// Start new search
function startNewSearch() {
    // Reset form data
    formData = {
        address: '',
        coordinates: null,
        name: '',
        email: '',
        phone: '',
        propertyType: '',
        bedrooms: '',
        bathrooms: '',
        priceRange: '',
        timeline: '',
        financing: ''
    };
    
    // Reset quiz
    currentQuestion = 0;
    
    // Clear form inputs
    document.getElementById('addressInput').value = '';
    document.getElementById('fullName').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    
    // Hide modal and show first step
    document.getElementById('successModal').classList.remove('show');
    showStep(1);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // If Google Maps is already loaded, initialize immediately
    if (window.google) {
        initializeApp();
    }
    // Otherwise, wait for the callback from the Google Maps script
});

// Global callback for Google Maps
window.initializeApp = initializeApp;
