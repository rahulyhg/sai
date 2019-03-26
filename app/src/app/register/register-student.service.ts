import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { Student } from '../student/student';
import { Observable, BehaviorSubject } from 'rxjs';
import { ResponseApi } from '../core/response-api';

@Injectable()

export class RegisterStudentService {

    private API = environment.ApiUrl;
    student = new BehaviorSubject<Student>(null);

    constructor(private http: HttpClient) {}

    register(newStudent: Student) {
        return this.http.post(this.API + '/student/register', newStudent, {observe: 'response'});
    }

    getStudentDataByEmail(email: string) {
        return this.http.post(this.API + '/student/getStudent', {email}, {observe: 'response'});
    }

    getStudentDataById(studentId: number) {
        return this.http.post(this.API + '/student/getStudent', {studentId}, {observe: 'response'});
    }

    basicRegister(basicStudent: Student) {
        return this.http.post(this.API + '/student/basicRegister', basicStudent, {observe: 'response'});
    }
}
