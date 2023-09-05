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
import OptionsHelper from '../../../helpers/OptionsHelper';
import { POST_TYPE_OPTIONS } from '../../../configs/options';

class Posts extends Component {
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
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handlePreviousPage = this.handlePreviousPage.bind(this);
    this.handleNextPage = this.handleNextPage.bind(this);
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
    this.props.history.push(`/admin/blogs/posts${QueryString.stringify(search, { addQueryPrefix: true })}`);
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
      response, body
    } = await Api.get('admin/blogs/posts', search);
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
    const data = await Api.put(`admin/blogs/posts/${id}/toggle-active`);
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
    const data = await Api.put(`admin/blogs/posts/${params.id}`, {
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
    const {
      search
    } = this.state;
    this.props.history.push(`/admin/blogs/posts/${data.id}${QueryString.stringify(search, { addQueryPrefix: true })}`, {
      data
    });
  }

  handleAdd() {
    const {
      search
    } = this.state;
    this.props.history.push(`/admin/blogs/posts/add${QueryString.stringify(search, { addQueryPrefix: true })}`);
  }

  async handleDelete(id, component) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/blogs/posts/${id}`);
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
    const postOption = OptionsHelper.getOption(search.post_type, POST_TYPE_OPTIONS, POST_TYPE_OPTIONS[0]);
    return (
      <Page
        title={postOption.label}
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            isSearching={isSearching}
            onSearch={this.handleSearch}
            onAdd={this.handleAdd}
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Posts));
