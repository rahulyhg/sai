import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/core/auth/auth.service';
import { ResponseApi } from 'src/app/core/response-api';


@Component({
    templateUrl: './signin.component.html'
})

export class SignInComponent implements OnInit {

    signinForm: FormGroup;

    constructor(
        private router: Router,
        private authService: AuthService,
        private formBuilder: FormBuilder) { }

    ngOnInit(): void {
        this.signinForm = this.formBuilder.group({
            email: ['',
                [
                    Validators.required,
                    Validators.email
                ]
            ],
            password: ['', Validators.required]
        });
    }

    login() {
        const email     = this.signinForm.get('email').value;
        const password  = this.signinForm.get('password').value;

        this.authService.authenticate(email, password)
            .subscribe(
                (res) => {
                    const response = res.body as ResponseApi;
                    if (!response.error) {
                        this.router.navigate(['dashboard']);
                    }
                },
                err => {
                    console.log(err);
                    this.signinForm.reset();
                    // criar função para alertar erro
                }
            );
    }
}
