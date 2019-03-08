import { Component, OnInit } from '@angular/core';
import { Student } from './student';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertService } from '../shared/services/alert/alert.service';
import { ResponseApi } from '../core/response-api';
import { StudentService } from './student.service';
import { environment } from 'src/environments/environment';

@Component({
    templateUrl: './student.component.html'
})

export class StudentComponent implements OnInit {

    private studentId;
    public student: Student;
    API = environment.ApiUrl;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private alertService: AlertService,
        private studentService: StudentService) {}

    ngOnInit(): void {
        this.studentId = this.route.snapshot.paramMap.get('s');

        // if (!this.studentId) { this.router.navigate(['dashboard']); }

        this.studentService.getStudent(this.studentId)
            .subscribe(res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.student = response.data as Student;
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao carregar as informações do estudante. Falha na comunicação com a API'));
    }

}
