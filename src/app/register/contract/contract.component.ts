import { Component, OnInit } from '@angular/core';
import { RegisterStudentService } from '../register-student.service';
import { Student } from '../student';
import { Observable } from 'rxjs';
import { ActivatedRoute } from '@angular/router';

@Component({
    templateUrl: './contract.component.html'
})

export class ContractComponent implements OnInit {

    student$: Observable<Student>;

    constructor(
        private registerStudentService: RegisterStudentService, 
        private activatedRoute: ActivatedRoute) {}

    ngOnInit(): void {
        this.activatedRoute.queryParams
        .subscribe(params => {
            this.student$ = this.registerStudentService.getStudent(params['studentEmail']);
        });
    }
}
