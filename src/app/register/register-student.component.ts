import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
    templateUrl: './register-student.component.html'
})

export class RegisterStudentComponent implements OnInit {

    registerStudentForm: FormGroup;
    step = 0;

    constructor(
        private formBuilder: FormBuilder,
        private router: Router) { }

    ngOnInit(): void {
        this.registerStudentForm = this.formBuilder.group({

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

}
