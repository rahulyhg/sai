import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { NewUser } from './new-user';

@Injectable()
export class SignUpService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    signup(newUser: NewUser) {
        return this.http.post(this.API + '/access/signup', newUser);
    }
}
