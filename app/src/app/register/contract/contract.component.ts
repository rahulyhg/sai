import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { RegisterStudentService } from '../register-student.service';
import { Student } from '../../student/student';
import { Observable } from 'rxjs';
import { ActivatedRoute } from '@angular/router';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { ResponseApi } from 'src/app/core/response-api';

@Component({
    templateUrl: './contract.component.html',
    styleUrls: ['./contract.component.scss']
})

export class ContractComponent implements OnInit {

    student: Student;
    today: number = Date.now();
    @ViewChild('registrationForm') registrationForm: ElementRef<HTMLDivElement>;
    @ViewChild('contract') contract: ElementRef<HTMLDivElement>;
    @ViewChild('contractStudent') contractStudent: ElementRef<HTMLDivElement>;

    constructor(
        private registerStudentService: RegisterStudentService,
        private activatedRoute: ActivatedRoute,
        private alertService: AlertService) {}

    ngOnInit() {
        this.activatedRoute.queryParams
        .subscribe(params => {
            this.registerStudentService.getStudentDataByEmail(params['email'])
                .subscribe( res => {
                    const response = res.body as ResponseApi;
                    ! response.error
                    ? this.student = response.data as Student
                    : this.alertService.error(response.error);
                }, err => this.alertService.error('Houve um erro ao buscar os dados do estudante. Falha na comunicação com a API'));
        },
        err => this.alertService.error('Não foi possível localizar o usuário.'));
    }

    printForm() {
        let print;

        print = window.open('', '_blank', 'top=0,left=0,width=auto, height=100%');
        print.document.write(`
                    <html>
                        <head>
                        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
                        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
                        <style>
                            ol {list-style-type: none;counter-reset: item;margin: 0;padding: 0;}
                            ol > li {display: table;counter-increment: item;margin-bottom: 0.6em;}
                            ol > li:before {content: counters(item, ".") ". ";display: table-cell;padding-right: 0.6em;}
                            li ol > li {margin: 0;}
                            li ol > li:before {content: counters(item, ".") " ";}
                        </style>
                        </head>
                        <body onload="window.print();window.close()">
                            <div style="margin: 10% 10% 10% 10%">${this.registrationForm.nativeElement.innerHTML}</div>
                        </body>
                    </html>`
        );
        print.document.close();
        print.focus();
        // print.close();
    }

    printContract() {
        let print;

        print = window.open('', '_blank', 'top=0,left=0,width=auto, height=100%');
        print.document.write(`
                    <html>
                        <head>
                        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
                        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
                        <style>
                            ol {list-style-type: none;counter-reset: item;margin: 0;padding: 0;}
                            ol > li {display: table;counter-increment: item;margin-bottom: 0.6em;}
                            ol > li:before {content: counters(item, ".") ". ";display: table-cell;padding-right: 0.6em;}
                            li ol > li {margin: 0;}
                            li ol > li:before {content: counters(item, ".") " ";}
                        </style>
                        </head>
                        <body onload="window.print();window.close()">
                            <div style="margin: 10% 10% 10% 10%">${this.contract.nativeElement.innerHTML}</body></div>
                    </html>`
        );
        print.document.close();
        print.focus();
        // print.close();
    }

    printContractStudent() {
        let print;

        print = window.open('', '_blank', 'top=0,left=0,width=auto, height=100%');
        print.document.write(`
                    <html>
                        <head>
                        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
                        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
                        <style>
                            ol {list-style-type: none;counter-reset: item;margin: 0;padding: 0;}
                            ol > li {display: table;counter-increment: item;margin-bottom: 0.6em;}
                            ol > li:before {content: counters(item, ".") ". ";display: table-cell;padding-right: 0.6em;}
                            li ol > li {margin: 0;}
                            li ol > li:before {content: counters(item, ".") " ";}
                        </style>
                        </head>
                        <body onload="window.print();window.close()">
                            <div style="margin: 10% 10% 10% 10%">${this.contractStudent.nativeElement.innerHTML}</div>
                        </body>
                    </html>`
        );
        print.document.close();
        print.focus();
        // print.close();
    }
}
