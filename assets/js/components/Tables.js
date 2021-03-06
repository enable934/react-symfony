import React, {Component} from 'react';
import axios from 'axios';
import $ from 'jquery';

class Tables extends Component {
    constructor() {
        super();
        this.state = {
            tables: [],
            loading: true,
            selectedTables: [],
            userName: '',
            date: '',
            timeFrom: '',
            timeTo: '',
            userPhone: '',
            userEmail: ''
        };
        this.dateHandler = this.dateHandler.bind(this);
        this.timeFromHandler = this.timeFromHandler.bind(this);
        this.timeToHandler = this.timeToHandler.bind(this);
        this.emailHandler = this.emailHandler.bind(this);
        this.userNameHandler = this.userNameHandler.bind(this);
        this.userPhoneHandler = this.userPhoneHandler.bind(this);
        this.onSubmitForm = this.onSubmitForm.bind(this);
    }

    componentDidMount() {
        this.getTables();
        this.setState({date: this.getDefaultDate()});
    }

    getTables() {
        axios.get(`http://localhost:82/api/${this.props.match.params.id}/tables`).then(tables => {
            this.setState({tables: tables.data, loading: false, selectedTables: []})
        })
    }

    dateHandler(event) {
        this.setState({date: event.target.value});
    };

    timeFromHandler(event) {
        this.setState({timeFrom: event.target.value});
    };

    timeToHandler(event) {
        this.setState({timeTo: event.target.value});
    };

    emailHandler(event) {
        this.setState({userEmail: event.target.value});
    };

    userNameHandler(event) {
        this.setState({userName: event.target.value});
    };

    userPhoneHandler(event) {
        this.setState({userPhone: event.target.value});
    };

    render() {
        const loading = this.state.loading;
        return (
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row">
                            <h2 className="text-center"><span>Усі столики</span><i className="fa fa-heart"/></h2>
                        </div>
                        <form method={'POST'} onSubmit={this.onSubmitForm}>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="date">Оберіть дату:
                                    <input defaultValue={this.state.date} className={"form-control"}
                                           onChange={this.dateHandler} type="date" id="date"
                                           name="date" required/>
                                </label>
                            </div>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="timeFrom">Виберіть час з котрої: </label>
                                <input className={"form-control"}
                                       onChange={this.timeFromHandler} id="timeFrom"
                                       type="time" name="timeFrom"
                                       required/>
                            </div>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="timeTo">Виберіть час до котрої: </label>
                                <input className={"form-control"}
                                       onChange={this.timeToHandler} id="timeTo" type="time"
                                       name="timeTo"
                                       required/>
                            </div>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="email">Ваш email:</label>
                                <input className={"form-control"} onChange={this.emailHandler} type="email" id="email"
                                       name="userEmail" required/>
                            </div>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="name">Ім'я (4 до 8 символів):</label>
                                <input className={"form-control"} onChange={this.userNameHandler} type="text" id="name"
                                       name="userName" required/>
                            </div>
                            <div className={'row form-group'}>
                                <label style={{color: 'white'}} htmlFor="phone">Ваш телефон:</label>
                                <input className={"form-control"} onChange={this.userPhoneHandler} type="tel" id="phone"
                                       name="userPhone"
                                       required/>
                            </div>
                            <select id={'select'} style={{display: 'none'}} multiple>
                                {this.state.tables.map(table => <option key={table.id}
                                                                        value={table.id}>{table.id}</option>)}
                            </select>
                            <button className="btn btn-block btn-primary" type={'submit'}>Підтвердити</button>
                            {loading ? (
                                <div className={'row text-center'}>
                                    <span className="fa fa-spin fa-spinner fa-4x"/>
                                </div>
                            ) : (
                                <div className={'row'}>
                                    {this.state.tables.map((table, index) =>
                                        <div className={"d-flex justify-content-around m-2"}>
                                            <div className="text-center"
                                                 style={{backgroundColor: this.state.selectedTables.includes(table.id) ? 'DodgerBlue' : 'white'}}
                                                 key={table.id}>
                                                <img alt={'Номер стола №' + table.number}
                                                     title={'Номер стола №' + table.number}
                                                     src={this.checkOrderByDateAndTime(table) ? (require('../../img/table-busy.png')) : (require('../../img/table.png'))}
                                                     width={200}/>
                                                <h5>№ {table.number}</h5>{this.checkOrderByDateAndTime(table)?
                                                <span className="badge badge-warning">занято: {this.getOrderByDateAndTime(table)}</span>:<span className="badge badge-success">вільно</span>}
                                                <button disabled={this.checkOrderByDateAndTime(table) ? 'disabled' : ''}
                                                        onClick={() => this.addToPocket(table)} type={'button'}
                                                        className="btn btn-outline-primary m-1">забронювати
                                                </button>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}
                        </form>
                    </div>
                </section>
            </div>
        )
    }

    onSubmitForm(event) {
        event.preventDefault();
        let loading = `<div id="loader" class="row text-center"><span class="fa fa-spin fa-spinner fa-4x"/></div>`;
        $('.container').prepend(loading);
        fetch('/api/order/new', {
            method: 'POST',
            body: JSON.stringify({
                date: this.state.date,
                timeFrom: this.state.timeFrom,
                timeTo: this.state.timeTo,
                userName: this.state.userName,
                userPhone: this.state.userPhone,
                userEmail: this.state.userEmail,
                tables: this.state.selectedTables,
                restaurant: this.props.match.params.id
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then((data) => {
            $('#loader').remove();
            let success = '<div class="alert alert-success alert-dismissible fade show" role="alert">\n' +
                `  <strong>${data.message}</strong> Ми чекатимемо вас у відповідний час.\n` +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>';
            let fail = '<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
                `  <strong>Виникла помилка!</strong> ${data.error}\n` +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>';
            if (data.error.length > 0) {
                return $('.container').prepend(fail);
            }
            this.getTables();
            return $('.container').prepend(success);
        });
    }

    addToPocket(table) {
        if (this.state.selectedTables.includes(table.id)) {
            this.state.selectedTables = this.state.selectedTables.filter((value) => {
                return value !== table.id
            });
            this.setState({selectedTables: this.state.selectedTables});
            $('#select > option[value=' + table.id + ']').attr('selected', false);
        } else {
            $('#select > option[value=' + table.id + ']').attr('selected', true);
            $('.col-md-10.offset-md-1.row-block');
            this.state.selectedTables.push(table.id);
            this.setState({selectedTables: this.state.selectedTables});
        }
        console.log(this.state);
    }

    checkOrderByDateAndTime(table) {
        const totalSecondsFrom = this.getTotalSecondsFrom();
        const totalSecondsTo = this.getTotalSecondsTo();
        for (const order of table.orders){
            if (this.state.date === JSON.parse(order.date).date.substr(0, 10)) {
                const orderTimeFrom = this.getOrderTimeFrom(order);
                const orderTimeTo = this.getOrderTimeTo(order);
                const totalOrderSecondsFrom = this.getTotalOrderSecondsFrom(orderTimeFrom);
                const totalOrderSecondsTo = this.getTotalOrderSecondsTo(orderTimeTo);
                if ((totalOrderSecondsFrom >= totalSecondsFrom && totalOrderSecondsTo <= totalSecondsTo)
                    || (totalSecondsFrom < totalOrderSecondsFrom && (totalSecondsTo >= totalOrderSecondsFrom && totalSecondsTo <= totalOrderSecondsTo))
                    || (totalSecondsTo > totalOrderSecondsFrom && (totalSecondsFrom >= totalOrderSecondsFrom && totalSecondsFrom <= totalOrderSecondsTo))) {
                    return true;
                }
            }
        }
        return false;
    }

    getOrderByDateAndTime(table) {
        const totalSecondsFrom = this.getTotalSecondsFrom();
        const totalSecondsTo = this.getTotalSecondsTo();
        for (const order of table.orders){
            if (this.state.date === JSON.parse(order.date).date.substr(0, 10)) {
                const orderTimeFrom = this.getOrderTimeFrom(order);
                const orderTimeTo = this.getOrderTimeTo(order);
                const totalOrderSecondsFrom = this.getTotalOrderSecondsFrom(orderTimeFrom);
                const totalOrderSecondsTo = this.getTotalOrderSecondsTo(orderTimeTo);
                if ((totalOrderSecondsFrom >= totalSecondsFrom && totalOrderSecondsTo <= totalSecondsTo)
                    || (totalSecondsFrom < totalOrderSecondsFrom && (totalSecondsTo >= totalOrderSecondsFrom && totalSecondsTo <= totalOrderSecondsTo))
                    || (totalSecondsTo > totalOrderSecondsFrom && (totalSecondsFrom >= totalOrderSecondsFrom && totalSecondsFrom <= totalOrderSecondsTo))) {
                    return `з ${orderTimeFrom.substr(11, 5)} по ${orderTimeTo.substr(11, 5)}`;
                }
            }
        }
        return '';
    }

    getDefaultDate() {
        let curr = new Date();
        let result = curr.toISOString().substr(0, 10);
        this.setState({date: result});
        return curr.toISOString().substr(0, 10);
    }

    getTotalSecondsFrom() {
        return Number.parseInt(this.state.timeFrom.substr(0, 2)) * 60
        + Number.parseInt(this.state.timeFrom.substr(3, 2));
    }

    getTotalSecondsTo() {
        return Number.parseInt(this.state.timeTo.substr(0, 2)) * 60
            + Number.parseInt(this.state.timeTo.substr(3, 2));
    }

    getOrderTimeFrom(order) {
        return JSON.parse(order.timeFrom).date;
    }

    getOrderTimeTo(order) {
        return JSON.parse(order.timeTo).date;
    }

    getTotalOrderSecondsFrom(orderTimeFrom) {
        return Number.parseInt(orderTimeFrom.substr(11, 2)) * 60
            + Number.parseInt(orderTimeFrom.substr(14, 2));
    }

    getTotalOrderSecondsTo(orderTimeTo) {
        return Number.parseInt(orderTimeTo.substr(11, 2)) * 60
            + Number.parseInt(orderTimeTo.substr(14, 2));
    }
}

export default Tables;