import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import * as textMaskAddOn from 'text-mask-addons';
import { Student } from '../student/student';
import { RegisterStudentService } from './register-student.service';
import { ResponseApi } from '../core/response-api';
import { AlertService } from '../shared/services/alert/alert.service';
import { MatDialog } from '@angular/material';
import { RegisterImageComponent } from './register-image/register-image.component';
import { ClassesService } from '../classes/classes.service';
import { Class } from '../classes/class';
import { UserService } from '../user/user.service';

@Component({
    templateUrl: './register-student.component.html'
})

export class RegisterStudentComponent implements OnInit {

    registerStudentForm: FormGroup;
    basicRegisterForm: FormGroup;
    step = 0;
    placeholder = 'u';
    buttomDisabled: boolean;
    basicButtomDisabled: boolean;
    studentImage;
    unitClasses: Class[];

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
        private registerStudentService: RegisterStudentService,
        private router: Router,
        private alertService: AlertService,
        private dialog: MatDialog,
        private classesService: ClassesService,
        private userService: UserService) { }

    ngOnInit(): void {
        this.getUnitClasses(this.userService.getUserUnit());
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
            email: ['',
                [
                    Validators.required,
                    Validators.email
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
                    Validators.minLength(5),
                    Validators.maxLength(80)
                ]
            ],
            responsibleParentage: ['',
                [
                    Validators.minLength(3),
                    Validators.maxLength(80)
                ]
            ],
            responsiblePhone: [''],
            responsibleCpf: ['',
                [
                    /* Validators.minLength(11),
                    Validators.maxLength(11) */
                ]
            ],
            responsibleRg: ['',
                [
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
                    Validators.minLength(4),
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
            studentImage: ['',
                [
                    Validators.required
                ]
            ],
            class: ['',
                [
                    Validators.required
                ]
            ],
            unit: [this.userService.getUserUnit(), {disabled: true}],
            paymentCash: [''],
            paymentCashDiscount: [''],
            paymentCashAmounth: [''],
            paymentInstallment: [''],
            paymentInstallmentParcels: [''],
            paymentInstallmentParcelsValue: ['']
        });
        this.basicRegisterForm = this.formBuilder.group({
            name: ['', Validators.required],
            email: ['',
                [
                    Validators.required,
                    Validators.email
                ]
            ],
            class: ['', Validators.required],
            unit: [this.userService.getUserUnit()]
        });
        this.buttomDisabled = false;
    }

    openRegisterImage() {
        const dialogRef = this.dialog.open(RegisterImageComponent, {
            width: 'auto',
            data: this.studentImage
        });

        dialogRef.afterClosed()
            .subscribe( result => {
                this.registerStudentForm.get('studentImage').setValue(result);
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

    getUnitClasses(unitId: number) {

        this.classesService.getUnitClasses(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.unitClasses = response.data as Class[];

                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao buscar as turmas. Falha na comunicação com a API'));
    }

    submit() {

        if (this.registerStudentForm.valid && !this.registerStudentForm.pending) {

            this.buttomDisabled = true;
            const newStudent = this.registerStudentForm.getRawValue() as Student;

            this.registerStudentService.register(newStudent)
            .subscribe(
                res => {
                    const response = res.body as ResponseApi;
                    this.buttomDisabled = false;

                    if (!response.error) {
                        this.router.navigate(['register/contract'],
                        {queryParams: {email: newStudent.email}});
                    } else {
                        this.alertService.error(response.error);
                        this.buttomDisabled = false;
                    }
                },
                err => {
                    this.alertService.error('Falha de comunicação com a API');
                    this.buttomDisabled = false;
                }
            );
        }

    }

    basicSubmit() {

        if (this.basicRegisterForm.valid && !this.basicRegisterForm.pending) {

            this.basicButtomDisabled = true;
            const basicStudent = this.basicRegisterForm.getRawValue() as Student;

            this.registerStudentService.basicRegister(basicStudent)
                .subscribe( res => {

                    const response = res.body as ResponseApi;
                    this.buttomDisabled = false;

                    if (!response.error) {
                        this.alertService.success('Estudante cadastrado com sucesso!');
                        this.basicRegisterForm.reset();
                    } else {
                        this.alertService.error(response.error);
                    }

                }, err => {
                        this.alertService.error('Houve um erro ao cadastrar o estudante. Falha na comunicação com a API');
                        this.buttomDisabled = false;
                });
        }

    }


}
