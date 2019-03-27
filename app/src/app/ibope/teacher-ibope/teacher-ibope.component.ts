import { Component, OnInit } from '@angular/core';
import { IbopeService } from '../ibope.service';
import { UserService } from 'src/app/user/user.service';
import { ResponseApi } from 'src/app/core/response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { MatTableDataSource, MatDialog } from '@angular/material';
import { TeacherIbopeDialogComponent } from './dialog/teacher-ibope-dialog.component';
import { TeacherIbope } from './teacher-ibope';
import { IbopeConfig } from '../ibope-config';

@Component({
    selector: 'app-teacher-ibope',
    templateUrl: './teacher-ibope.component.html'
})

export class TeacherIbopeComponent implements OnInit {

    teachersIbope: [];
    displayedColumns: string[];
    dataSource;
    teacherIbopeData: TeacherIbope;
    ibopeConfig: IbopeConfig;

    constructor(
        private ibopeService: IbopeService,
        private userService: UserService,
        private alertService: AlertService,
        public dialog: MatDialog) {}

    ngOnInit(): void {
        this.getIbopeConfig(this.userService.getUserUnit());
        this.displayedColumns = [
            'teacher',
            'discipline',
            'reply'
        ];
    }

    getTeachersIbope(userClass: number, userId: number, month: number): void {
        this.ibopeService.getStudentTeachersIbope(userClass, userId, this.ibopeConfig.ibopeMonth)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.teachersIbope = response.data as [];
                    this.dataSource = new MatTableDataSource(this.teachersIbope);
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao buscar os professores. Falha na comunciação com a API'));
    }

    openDialog(): void {
        const dialogRef = this.dialog.open(TeacherIbopeDialogComponent, {
            width: 'auto',
            minWidth: '50%',
            disableClose: true,
            data: this.teacherIbopeData
        });

        dialogRef.afterClosed()
            .subscribe(res => {

                if (res) {

                    this.teacherIbopeData = res;
                    this.saveIbope();
                }
            });
    }

    reply(teacherId: number, teacherName: string, discipline: string, tdId: number): void {

        this.resetTeacherIbopeData();
        this.teacherIbopeData.teacherId             = teacherId;
        this.teacherIbopeData.teacherName           = teacherName;
        this.teacherIbopeData.discipline            = discipline;
        this.teacherIbopeData.teacherDisciplineId   = tdId;
        this.teacherIbopeData.month                 = this.ibopeConfig.ibopeMonth;
        this.openDialog();
    }

    resetTeacherIbopeData() {
        this.teacherIbopeData = {
            month: 0,
            msg: '',
            rating: 0,
            studentId: this.userService.getUserId(),
            teacherId: 0,
            teacherName: '',
            discipline: '',
            teacherDisciplineId: 0
        };
    }

    private saveIbope() {
        this.ibopeService.registerTeacherIbope(this.teacherIbopeData)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.alertService.success('IBOPE registrado com sucesso');
                    this.getIbopeConfig(this.userService.getUserUnit());
                } else {
                    this.alertService.error(response.error);
                    this.resetTeacherIbopeData();
                }
            });
    }

    private getIbopeConfig(unitId: number) {
        this.ibopeService.getIbopeConfig(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.ibopeConfig = response.data as IbopeConfig;
                    this.getTeachersIbope(this.userService.getUserClass(), this.userService.getUserId(), this.ibopeConfig.ibopeMonth);

                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as configurações do Ibope. Falha na comunicação com a API'));
    }

}
