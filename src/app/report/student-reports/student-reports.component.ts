import { Component, OnInit } from '@angular/core';
import { StudentReportService } from './student-report.service';
import { ResponseApi } from 'src/app/core/response-api';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { StudentReport } from './student-report';
import { MatTableDataSource } from '@angular/material';
import { Student } from 'src/app/student/student';
import { StudentService } from 'src/app/student/student.service';

@Component({
    templateUrl: './student-report.component.html'
})

export class StudentReportComponent implements OnInit {

    students: Student[];
    displayedColumns: string[];
    dataSource;

    constructor(
        private studentReportService: StudentReportService,
        private alertService: AlertService,
        private studentService: StudentService) {
        this.getStudents();
    }

    ngOnInit(): void {
        // colunas a serem renderizadas na tabela
        this.displayedColumns = [
            'name',
            // 'email',
            'phone',
            'date',
            'city',
            'classCourse',
            'classPeriod',
            'paymentMethod',
            'discountPercent',
            'paymentCashAmounth',
            'installments',
            'installmentsValue',
            'remove'
        ];
    }

    getStudents() {
        this.studentReportService.getStudents()
            .subscribe(
                res => {
                    const response = res.body as ResponseApi;
                    if (!response.error) {
                       this.students = response.data as Array<Student>;
                       this.dataSource = new MatTableDataSource(this.students);

                       this.dataSource.filterPredicate =
                       (data: StudentReport, filter: string) => {
                           const filters = filter.split('-');

                           // tslint:disable-next-line:triple-equals
                           if (data.classCourse == filters[0] && data.classPeriod == filters[1]) {
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

    applyFilter(filter) {
        this.dataSource.filter = filter.trim().toLowerCase();
    }

    public removeStudent(studentId: number) {
        this.studentService.removeStudent(studentId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.getStudents();
                } else {
                    this.alertService.error(response.error);
                }
            })
    }

}
