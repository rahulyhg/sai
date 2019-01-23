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

    getStudent(email: string): Observable<Student> {
        this.getStudentData(email);
        return this.student.asObservable();
    }

    private getStudentData(email: string) {
        this.http.post(this.API + '/student/getStudent', {email}, {observe: 'response'})
            .subscribe(res => {
                const response = res.body as ResponseApi;
                if (!response.error) {
                    const student = response.data as Student;
                    console.log('res.data as student', student);
                    this.student.next(student);
                }
            });
    }
}
