/**
 * Form Validation & Client-side Security
 * Validation avant envoi, feedback UX, prévention XSS
 */

class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.setupListeners();
    }

    setupListeners() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Validation en temps réel pour certains champs
        this.form.querySelectorAll('[data-validate]').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.validateField(input));
        });
    }

    handleSubmit(e) {
        const allValid = this.validateForm();
        if (!allValid) {
            e.preventDefault();
            showNotification('Veuillez corriger les erreurs du formulaire', 'error');
        }
    }

    validateForm() {
        let isValid = true;
        this.form.querySelectorAll('[data-validate]').forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    validateField(input) {
        const rules = input.dataset.validate.split('|');
        let isValid = true;
        const value = input.value.trim();

        // Effacer les erreurs précédentes
        input.classList.remove('invalid');
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('field-error')) {
            errorElement.remove();
        }

        // Exécuter les règles de validation
        for (const rule of rules) {
            if (!this.checkRule(rule, value, input)) {
                isValid = false;
                break;
            }
        }

        // Afficher erreur ou succès visuel
        if (!isValid) {
            input.classList.add('invalid');
            const error = document.createElement('small');
            error.classList.add('field-error');
            error.textContent = this.getErrorMessage(rules[0], input);
            input.parentElement.appendChild(error);
        } else {
            input.classList.add('valid');
        }

        return isValid;
    }

    checkRule(rule, value, input) {
        const rules_list = {
            required: () => value.length > 0,
            email: () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || value.length === 0,
            phone: () => /^[0-9+\-\s()]{8,}$/.test(value) || value.length === 0,
            minlength: () => value.length >= parseInt(input.minLength || 0),
            maxlength: () => value.length <= parseInt(input.maxLength || 9999),
            match: () => value === document.querySelector(input.dataset.match)?.value || !input.dataset.match,
            nospecial: () => !/[<>\"'%&;]/.test(value),
            pdf_file: () => input.files.length === 0 || input.files[0].type === 'application/pdf'
        };

        return (rules_list[rule] || (() => true))();
    }

    getErrorMessage(rule, input) {
        const messages = {
            required: 'Ce champ est requis',
            email: 'Veuillez entrer une adresse email valide',
            phone: 'Veuillez entrer un téléphone valide (8+ chiffres)',
            minlength: `Minimum ${input.minLength} caractères requis`,
            maxlength: `Maximum ${input.maxLength} caractères autorisés`,
            match: 'Les valeurs ne correspondent pas',
            nospecial: 'Caractères spéciaux non autorisés',
            pdf_file: 'Seuls les fichiers PDF sont acceptés'
        };
        return messages[rule] || 'Validation échouée';
    }
}

// Initialiser validation de tous les formulaires
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-validate="true"]').forEach(form => {
        new FormValidator(form);
    });
});
