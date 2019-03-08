import { Component, OnInit } from '@angular/core';
import { Class } from '../class';
import { ClassesService } from '../classes.service';
import { ResponseApi } from 'src/app/core/response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { UserService } from 'src/app/user/user.service';
import { Student } from 'src/app/student/student';
import { environment } from 'src/environments/environment';
import { Router } from '@angular/router';

@Component({
    templateUrl: './unit-classes.component.html'
})

export class UnitClassesComponent implements OnInit {

    unitClasses: Class[];
    classStudents: Student[];
    API = environment.ApiUrl;

    constructor(
        private classesService: ClassesService,
        private alertService: AlertService,
        private userService: UserService,
        private router: Router) {}

    ngOnInit() {
        this.getUnitClasses(this.userService.getUserUnit());
    }

    private getUnitClasses(unitId: number) {

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

    getClassStudents(classId: number) {

        this.classesService.getClassStudents(classId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.classStudents = response.data as Student[];
                } else {
                    this.alertService.error(response.error);
                    this.classStudents = [];
                }
            }, err => this.alertService.error('Houve um erro ao buscar os estudantes da turma selecionada. Fala na comunicação com a API'));
    }

    openStudent(studentId) {
        this.router.navigate(['student/' + studentId]);
    }
}
