import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { passwordConfirmValidator } from './password-confirm.validator';
import { SignUpService } from './signup.service';
import { NewUser } from './new-user';
import { Router } from '@angular/router';

@Component({
    templateUrl: './signup.component.html'
})


export class SignUpComponent implements OnInit {

    signupForm: FormGroup;

    constructor (
        private formBuilder: FormBuilder,
        private signupService: SignUpService,
        private router: Router
    ) {}

    ngOnInit(): void {
        this.signupForm = this.formBuilder.group({
            fullName: ['',
                [
                    Validators.required,
                    Validators.minLength(6),
                    Validators.maxLength(40)
                ]
            ],
            email: ['',
                [
                    Validators.required,
                    Validators.email
                ]
            ],
            password: ['',
                [
                    Validators.required,
                    Validators.minLength(6),
                    Validators.maxLength(14)
                ]
            ],
            cpf: ['',
                [
                    Validators.required,
                    Validators.minLength(11),
                    Validators.maxLength(11)
                ]
            ],
            phone: ['',
                [
                    Validators.required,
                    Validators.minLength(10),
                    Validators.maxLength(11)
                ]
            ],
            passwordConfirm: ['']
        },
        {
            validator: passwordConfirmValidator
        });
    }

    signup() {
        if (this.signupForm.valid && !this.signupForm.pending) {
            const newUser = this.signupForm.getRawValue() as NewUser;
            console.log(newUser);
            this.signupService
                .signup(newUser)
                .subscribe(
                    a => {
                        console.log('cadastrou');
                        this.router.navigate(['/signin']);
                    },
                    err => console.log(err)
                );
        }
    }
}
