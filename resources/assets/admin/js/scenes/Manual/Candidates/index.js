/* eslint-disable no-param-reassign */
/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import QueryString from 'qs';
import {
  Page, Card, Spinner
} from '@shopify/polaris';
import InfiniteScroll from 'react-infinite-scroller';

import Api from '../../../apis/app';
import Action from './Action';
import Item from './Item';
import Add from './Add';
import Header from './Header';
import OptionsHelper from '../../../helpers/OptionsHelper';

class Candidates extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      pagination: {
        data: []
      },
      search: {
        inactive: false,
        name: '',
      },
      isSearching: false,
      isSearchingMore: false
    };

    this.handleSearch = this.handleSearch.bind(this);
    this.handleSearchMore = this.handleSearchMore.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
  }

  async componentDidMount() {
    const categoriesRes = await Api.get('admin/manual/categories');
    const { body: categories } = categoriesRes;
    const countriesRes = await Api.get('admin/manual/countries');
    const { body: countries } = countriesRes;
    const candidateTypesRes = await Api.get('admin/manual/candidate-types');
    const { body: candidateTypes } = candidateTypesRes;
    const search = QueryString.parse(this.props.location.search, { ignoreQueryPrefix: true });
    search.inactive = search.inactive === 'true';
    if (categories.length > 0 && !search.category_id) {
      search.category_id = categories[0].id;
    }
    if (!search.candidate_type_id) {
      search.candidate_type_id = '0';
    }
    search.is_player = 1;

    await this.setState({
      categories: OptionsHelper.getOptions(categories, 'id', 'name'),
      countries: OptionsHelper.getOptions(countries, 'id', 'name', { value: '0', label: 'Select...' }),
      candidateTypes: OptionsHelper.getOptions(candidateTypes, 'id', 'name', { value: '0', label: 'Select...' }),
      search
    });
    this.search();
  }

  async componentWillReceiveProps(props) {
    if (props.location.search !== this.props.location.search) {
      const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
      search.inactive = search.inactive === 'true';
      const { categories } = this.state;
      if (categories.length > 0) {
        if (!search.category_id) {
          search.category_id = categories[0].id;
        }
      }
      if (!search.candidate_type_id) {
        search.candidate_type_id = '0';
      }
      await this.setState({
        search
      }, () => {
        this.search();
      });
    }
  }

  async search(isSearching = true) {
    const { search } = this.state;
    this.setState({
      isSearching
    });
    const data = await Api.get('admin/manual/candidates', search);
    const {
      response, body
    } = data;

    switch (response.status) {
      case 200:
        this.setState({
          pagination: body
        });
        break;
      default:
        break;
    }
    this.setState({
      isSearching: false
    });
  }

  async handleSearchMore() {
    const { search, pagination } = this.state;
    if (!pagination.next_page_url) return;
    this.setState({
      isSearchingMore: true
    });
    const params = {
      ...search,
      page: pagination.current_page + 1
    };

    const data = await Api.get('admin/manual/candidates', params);
    const {
      response, body
    } = data;

    switch (response.status) {
      case 200:
        body.data = [
          ...pagination.data,
          ...body.data
        ];
        this.setState({
          pagination: body
        });
        break;
      default:
        break;
    }
    this.setState({
      isSearchingMore: false
    });
  }

  async handleSearch(search) {
    const url = `/admin/manual/candidates${QueryString.stringify(search, { addQueryPrefix: true })}`;
    this.props.history.push(url);
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      search
    } = this.state;

    values.category_id = search.category_id;
    const data = await Api.post('admin/manual/candidates', values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        bags.setValues({
          country_id: '0',
          candidate_type_id: '0',
          logo: '',
          name: '',
          name_es: '',
          short_name: '',
          short_name_es: '',
          slug: '',
          slug_es: '',
          meta_keywords: '',
          meta_keywords_es: '',
          meta_description: '',
          meta_description_es: '',
          meta: {
            content: ''
          },
          meta_es: {
            content: ''
          }
        });
        bags.setTouched({
          candidate_type_id: false,
          country_id: false,
          logo: false,
          name: false,
          name_es: false,
          short_name: false,
          short_name_es: false,
          slug: false,
          slug_es: false,
          meta_keywords: false,
          meta_keywords_es: false,
          meta_description: false,
          meta_description_es: false,
          meta: false,
          meta_es: false
        });
        this.search();
        break;
      default:
        break;
    }
    bags.setSubmitting(false);
    return data;
  }

  async handleEdit(id, values, bags, component) {
    bags.setSubmitting(true);
    const data = await Api.put(`admin/manual/candidates/${id}`, values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        this.search();
        break;
      default:
        break;
    }
    component.toggleEditMode();
    bags.setSubmitting(false);
    return data;
  }

  async handleToggleActive(id) {
    const data = await Api.put(`admin/manual/candidates/${id}/toggle-active`);
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

  async handleDelete(id, component) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/manual/candidates/${id}`);
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
      pagination, isSearching, isSearchingMore, categories, candidateTypes, countries, search
    } = this.state;
    return (
      <Page
        title="Candidates"
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            categories={categories}
            candidateTypes={candidateTypes}
            onSearch={this.handleSearch}
          />
        </Card>
        <Card sectioned>
          <Header />
          <InfiniteScroll
            loadMore={this.handleSearchMore}
            hasMore={(!!pagination.next_page_url) && !isSearchingMore}
            loader={<Spinner size="small" />}
          >
            {
              pagination.data.map((data, index) => (
                <div key={`${index}`}>
                  <Item
                    countries={countries}
                    candidateTypes={candidateTypes}
                    data={data}
                    isLoading={isSearching}
                    onEdit={this.handleEdit}
                    onDelete={this.handleDelete}
                    onToggleActive={this.handleToggleActive}
                  />
                </div>
              ))
            }
          </InfiniteScroll>
        </Card>
        <Add
          onAdd={this.handleAdd}
          countries={countries}
          candidateTypes={candidateTypes}
        />
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Candidates));
