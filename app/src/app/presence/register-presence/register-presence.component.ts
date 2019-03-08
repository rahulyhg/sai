import { Component, OnInit } from '@angular/core';
import { PresenceService } from '../presence.service';
import { Class } from 'src/app/classes/class';
import { Student } from 'src/app/student/student';
import { environment } from 'src/environments/environment';
import { UserService } from 'src/app/user/user.service';
import { ClassesService } from 'src/app/classes/classes.service';
import { ResponseApi } from 'src/app/core/response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { MatTableDataSource } from '@angular/material';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({
    templateUrl : './register-presence.component.html'
})

export class RegisterPresenceComponent implements OnInit {

    unitClasses: Class[];
    classStudents: Student[];
    API = environment.ApiUrl;
    displayedColumns: string[];
    dataSource;
    presences;
    presenceWithCardForm: FormGroup;

    constructor(
        private presenceService: PresenceService,
        private userService: UserService,
        private classesService: ClassesService,
        private alertService: AlertService,
        private formBuilder: FormBuilder) {}

    ngOnInit() {
        this.presenceWithCardForm = this.formBuilder.group({
            registerNumber: ['', Validators.required]
        });

        this.getUnitClasses(this.userService.getUserUnit());
        this.displayedColumns = [
            'name',
            'present',
            'away',
            'delayed',
            'justified'
        ];
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
                    this.dataSource = new MatTableDataSource(this.classStudents);
                } else {
                    this.alertService.error(response.error);
                    this.classStudents = [];
                }
            }, err => this.alertService.error('Houve um erro ao buscar os estudantes da turma selecionada. Fala na comunicação com a API'));

        this.getPresences(classId);
    }

    setStatusPresenceStudent(studentId: number, status: string) {
        this.presenceService.setStatusPresenceStudent(studentId, status)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (response.error) {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao registrar a presença. Falha na comunicação com a API'));
    }

    getPresences(classId: number) {
        this.presenceService.getPresences(classId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.presences = response.data;
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um problema ao buscar as presenças da turma. Falha na comunicação com a API'));
    }

    verifyStatus(studentId: number, status: string): boolean {
        let retorno = false;
        if (this.presences) {
            this.presences.find(presenca => {
                if (presenca.studentId === studentId && presenca.status === status) {
                    retorno = true;
                }
            });
        }

        return retorno;
    }

    submitPresenceWithCard() {
        this.presenceService.setStatusPresenceWithCard(this.presenceWithCardForm.get('registerNumber').value)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.presenceWithCardForm.reset();
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao salvar a presença. Falha na comunicação com a API'));
    }
}
