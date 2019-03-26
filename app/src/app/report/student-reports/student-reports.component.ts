import { Component, OnInit } from '@angular/core';
import { StudentReportService } from './student-report.service';
import { ResponseApi } from 'src/app/core/response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { StudentReport } from './student-report';
import { MatTableDataSource } from '@angular/material';
import { Student } from 'src/app/student/student';
import { StudentService } from 'src/app/student/student.service';
import { ClassesService } from 'src/app/classes/classes.service';
import { Class } from 'src/app/classes/class';
import { UserService } from 'src/app/user/user.service';

@Component({
    templateUrl: './student-report.component.html'
})

export class StudentReportComponent implements OnInit {

    unitClasses: Class[];
    students: Student[];
    displayedColumns: string[];
    dataSource;

    constructor(
        private studentReportService: StudentReportService,
        private alertService: AlertService,
        private studentService: StudentService,
        private userService: UserService,
        private classesService: ClassesService) {
    }

    ngOnInit(): void {
        this.getStudents();
        this.getUnitClasses(this.userService.getUserUnit());
        // colunas a serem renderizadas na tabela
        this.displayedColumns = [
            'name',
            // 'email',
            'phone',
            'date',
            'city',
            'className',
            'paymentMethod',
            'discountPercent',
            'paymentCashAmounth',
            'installments',
            'installmentsValue',
            'remove'
        ];
    }

    private getStudents() {
        this.studentReportService.getStudents()
            .subscribe(
                res => {
                    const response = res.body as ResponseApi;
                    if (!response.error) {
                       this.students = response.data as Array<Student>;
                       this.dataSource = new MatTableDataSource(this.students);

                       this.dataSource.filterPredicate =
                       (data: StudentReport, filter: number) => {

                           // tslint:disable-next-line:triple-equals
                           if (data.class == filter) {
                               return true;
                           } else {
                               return false;
                           }
                       };

                    } else {
                        this.alertService.error(response.error);
                    }
                }, err => {
                    this.alertService.error(err);
                }
            );
    }

    public applyFilter(filter) {
        this.dataSource.filter = filter;
    }

    public removeStudent(studentId: number) {
        if (confirm('Deseja realmente excluir o estudante?')) {

            this.studentService.removeStudent(studentId)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {
                        this.getStudents();
                    } else {
                        this.alertService.error(response.error);
                    }
                });
        }
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

}
