import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import * as textMaskAddOn from 'text-mask-addons';

@Component({
    templateUrl: './register-student.component.html'
})

export class RegisterStudentComponent implements OnInit {

    registerStudentForm: FormGroup;
    step = 0;
    placeholder = 'u';

    // mascaras

    maskCpf = [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/];
    maskPhone = ['(', /\d/, /\d/, ')', ' ', /\d/, ' ', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/];
    maskCEP = [/\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/];
    maskNumber = textMaskAddOn.createNumberMask({
        prefix: '',
        suffix: '',
        includeThousandsSeparator: false,
        requireDecimal: false
    });
    maskReal = textMaskAddOn.createNumberMask({
        prefix: 'R$ ',
        suffix: '',
        thousandsSeparatorSymbol: '.',
        allowDecimal: true,
        decimalSymbol: ',',
        integerLimit: 5
    });

    // end mascaras

    constructor(
        private formBuilder: FormBuilder,
        private router: Router) { }

    ngOnInit(): void {
        this.registerStudentForm = this.formBuilder.group({
            name: ['',
                [
                    Validators.required,
                    Validators.minLength(6),
                    Validators.maxLength(40)
                ]
            ],
            cpf: ['',
                [
                    Validators.required
                ]
            ],
            rg: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(15)
                ]
            ],
            shipping: ['',
                [
                    Validators.required,
                    Validators.minLength(2),
                    Validators.maxLength(30)
                ]
            ],
            birth: ['',
                [
                    Validators.required,
                    // Validators.pattern(/^\d{2}\[\/]\d{2}\[\/]\d{4}$/)
                ]
            ],
            studentPhone: ['',
                [
                    Validators.required
                ]
            ],
            hand: ['',
                [
                    Validators.required
                ]
            ],
            underAge: [''],
            address: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            number: ['',
                [
                    Validators.required,
                    Validators.maxLength(5)
                ]
            ],
            district: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            complement: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            city: ['',
                [
                    Validators.required,
                    Validators.minLength(3),
                    Validators.maxLength(80)
                ]
            ],
            state: ['',
                [
                    Validators.required,
                    Validators.minLength(2),
                    Validators.maxLength(2)
                ]
            ],
            cep: ['',
                [
                    Validators.required,
                    /* Validators.minLength(8),
                    Validators.maxLength(8) */
                ]
            ],
            responsibleName: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            responsibleParentage: ['',
                [
                    Validators.required,
                    Validators.minLength(3),
                    Validators.maxLength(80)
                ]
            ],
            responsiblePhone: ['',
                [
                    Validators.required
                ]
            ],
            responsibleCpf: ['',
                [
                    Validators.required,
                    /* Validators.minLength(11),
                    Validators.maxLength(11) */
                ]
            ],
            responsibleRg: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            emergencyName: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            emergencyParentage: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            emergencyPhone: ['',
                [
                    Validators.required,
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            classPeriod: ['',
                [
                    Validators.required
                ]
            ],
            classCourse: ['',
                [
                    Validators.required
                ]
            ],
            formed: ['',
                [
                    Validators.required
                ]
            ],
            formedYear: ['',
                [
                    Validators.minLength(4),
                    Validators.maxLength(4)
                ]
            ],
            formedSchool: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            formedSchoolCity: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            preEntrance: ['',
                [
                    Validators.required
                ]
            ],
            preEntranceName: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            preEntranceCity: ['',
                [
                    Validators.minLength(4),
                    Validators.maxLength(50)
                ]
            ],
            pretensionCourses: ['',
                [
                    Validators.minLength(4),
                    Validators.maxLength(50)
                ]
            ],
            pretensionUniversities: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            enem: ['',
                [
                   Validators.required
                ]
            ],
            controlledMedication: ['',
                [
                    Validators.required
                ]
            ],
            controlledMedicationDescription: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            specialNeed: ['',
                [
                    Validators.required
                ]
            ],
            specialNeedDescription: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            allergy: ['',
                [
                    Validators.required
                ]
            ],
            allergyDescription: ['',
                [
                    Validators.minLength(5),
                    Validators.maxLength(50)
                ]
            ],
            courseInformation: ['',
                [
                    Validators.required
                ]
            ],
            paymentCash: [''],
            paymentCashDiscount: [''],
            paymentCashAmounth: [''],
            paymentInstallment: [''],
            paymentInstallmentParcels: [''],
            paymentInstallmentParcelsValue: ['']
        });
    }


    setStep(index: number) {
        this.step = index;
    }

    nextStep() {
        this.step++;
    }

    prevStep() {
        this.step--;
    }

    submit() {
        console.log(this.registerStudentForm.getRawValue());
        console.log('erro do preentrance', this.registerStudentForm.get('preEntrance').errors);
    }

}
