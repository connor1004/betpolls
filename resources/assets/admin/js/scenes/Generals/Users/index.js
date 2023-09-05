/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import QueryString from 'qs';
import {
  Page, Card, Pagination
} from '@shopify/polaris';

import Api from '../../../apis/app';
import Action from './Action';
import Header from './Header';
import Item from './Item';

class Users extends Component {
  constructor(props) {
    super(props);
    this.state = {
      pagination: {
        data: []
      },
      search: {},
      isSearching: false
    };

    this.handleSearch = this.handleSearch.bind(this);
    this.handleAddUser = this.handleAddUser.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleToggleConfirmed = this.handleToggleConfirmed.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handlePreviousPage = this.handlePreviousPage.bind(this);
    this.handleNextPage = this.handleNextPage.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
  }

  async componentDidMount() {
    this.componentWillReceiveProps(this.props);
  }

  async componentWillReceiveProps(props) {
    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
    await this.setState({
      search
    });
    this.search();
  }

  async handleSearch(search) {
    this.props.history.push(`/admin/generals/users${QueryString.stringify(search, { addQueryPrefix: true })}`);
  }

  async handleNextPage() {
    const {
      search, pagination
    } = this.state;
    search.page = pagination.current_page + 1;
    await this.handleSearch(search);
  }

  async handlePreviousPage() {
    const {
      search, pagination
    } = this.state;
    search.page = pagination.current_page - 1;
    await this.handleSearch(search);
  }

  async search(isSearching = true) {
    const {
      search
    } = this.state;

    await this.setState({
      isSearching
    });

    const {
      body, response
    } = await Api.get('admin/generals/users', search);
    switch (response.status) {
      case 200:
      case 201:
        this.setState({
          pagination: body
        });
        break;
      default:
        break;
    }
    await this.setState({
      isSearching: false
    });
    this.prevSearch = search;
  }

  async handleToggleActive(id) {
    const data = await Api.put(`admin/generals/users/${id}/toggle-active`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        this.search();
        break;
      default:
        break;
    }
    return data;
  }

  async handleToggleConfirmed(params) {
    const data = await Api.put(`admin/generals/users/${params.id}`, {
      confirmed: params.confirmed === 1 ? 0 : 1
    });

    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        this.search();
        break;
      default:
        break;
    }
    return data;
  }

  handleDetail(data) {
    this.props.history.push(`/admin/generals/users/${data.id}`, {
      data
    });
  }

  handleAddUser() {
    this.props.history.push('/admin/generals/users/add');
  }

  async handleDelete(id, component) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/generals/users/${id}`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        this.search();
        break;
      default:
        break;
    }
    component.setDeleting(false);
    return data;
  }

  render() {
    const {
      pagination, isSearching, search
    } = this.state;
    return (
      <Page
        title="Users"
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            isSearching={isSearching}
            onSearch={this.handleSearch}
            onToggleAdd={this.handleAddUser}
            />
        </Card>
        <Card sectioned>
          <Header />
          {
            pagination.data.map((data, index) => (
              <Item
                key={`${index}`}
                data={data}
                onDetail={this.handleDetail}
                onToggleActive={this.handleToggleActive}
                onToggleConfirmed={this.handleToggleConfirmed}
                onDelete={this.handleDelete}
              />
            ))
          }
          <Pagination
            hasPrevious={pagination.current_page && pagination.current_page > 1}
            onPrevious={this.handlePreviousPage}
            onNext={this.handleNextPage}
            hasNext={pagination.current_page && pagination.current_page < pagination.last_page}
          />
        </Card>
      </Page>
    );
  }
}


const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = () /* dispatch */ => ({
  // logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Users));
