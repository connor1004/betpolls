/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter, Route } from 'react-router-dom';
import QueryString from 'qs';
import {
  Page, Card, Button, ButtonGroup, Modal, Stack, TextContainer
} from '@shopify/polaris';
import moment from 'moment';

import Api from '../../../apis/app';
import Action from './Action';
import OptionsHelper from '../../../helpers/OptionsHelper';
import Add from './Add';
import Header from './Header';
import Item from './Item';

class Polls extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      subcategories: [],
      list: [],
      search: {},
      isSearching: false,
      showDeleteConfirm: false,
      isDeleting: false,
      isTogglingActive: false
    };
    this.prevSearch = {};

    this.handleSearch = this.handleSearch.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handleToggleSelectItem = this.handleToggleSelectItem.bind(this);
    this.handleToggleSelectAll = this.handleToggleSelectAll.bind(this);
    this.handleDeleteSelectedItems = this.handleDeleteSelectedItems.bind(this);
    this.handleToggleActiveSelectedItems = this.handleToggleActiveSelectedItems.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  async componentDidMount() {
    const {
      body: categories
    } = await Api.get('admin/manual/categories');

    await this.setState({
      categories: OptionsHelper.getOptions(categories, 'id', 'name')
    });
    this.componentWillReceiveProps(this.props);
  }

  async componentWillReceiveProps(props) {
    const {
      categories
    } = this.state;
    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });

    if (!search.category_id) {
      if (categories.length > 0) {
        search.category_id = categories[0].value;
      }
    }

    const {
      body
    } = await Api.get(`admin/manual/subcategories`, {category_id: search.category_id});
    const subcategories = OptionsHelper.getOptions(body, 'id', 'name', { value: '0', label: 'Select...'});

    if (!search.start_at) {
      search.start_at = moment().subtract(1, 'months').format('YYYY-MM-DD');
      search.end_at = moment().add(1, 'months').format('YYYY-MM-DD');
    }
    await this.setState({
      search,
      subcategories
    });
    this.search();
  }

  async handleSearch(search) {
    this.props.history.push(`/admin/manual/polls${QueryString.stringify(search, { addQueryPrefix: true })}`);
  }

  async search(isSearching = true) {
    const {
      search
    } = this.state;

    if (this.prevSearch.category_id !== search.category_id) {
      const {
        body
      } = await Api.get(`admin/manual/subcategories`, {category_id: search.category_id});
      const subcategories = OptionsHelper.getOptions(body, 'id', 'name', { value: '0', label: 'Select...'});
      search.subcategory_id = '0';
      await this.setState({
        subcategories,
        search
      });
      
    }
    await this.setState({
      isSearching
    });

    const {
      body, response
    } = await Api.get('admin/manual/poll-pages', search);
    console.log(body);
    switch (response.status) {
      case 200:
      case 201:
        this.setState({
          list: body
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

  async handleAdd(values, bags, components) {
    const {
      search
    } = this.state;
    const {
      body, response
    } = await Api.post('admin/manual/poll-pages', { ...values, category_id: search.category_id });
    switch (response.status) {
      case 422:
        bags.setStatus(body.error);
        break;
      case 200:
        await bags.setValues(components.paramsToFormValues({}));
        await bags.setTouched({});
        this.search();
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  async handleEdit(id, values, bags, component) {
    bags.setSubmitting(true);
    const data = await Api.put(`admin/manual/poll-pages/${id}`, values);
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
    const data = await Api.put(`admin/manual/poll-pages/${id}/toggle-active`);
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
    const data = await Api.delete(`admin/manual/poll-pages/${id}`);
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

  handleDetail(data) {
    // window.open(`/admin/manual/polls/${data.id}`, '_blank');
    this.props.history.push(`/admin/manual/polls/${data.id}`);
  }

  handleToggleSelectItem(item) {
    item.selected = !item.selected;
    this.setState({
      list: this.state.list
    });
  }

  handleToggleSelectAll() {
    const {
      list
    } = this.state;

    const selected = !this.isSelectedAll;
    list.forEach((item) => {
      item.selected = selected;
    });
    this.setState({
      list
    });
  }

  get isSelectedAll() {
    const {
      list
    } = this.state;

    let selected = true;
    for (let i = 0, ni = list.length; i < ni; i++) {
      if (!list[i].selected) {
        selected = false;
        break;
      }
    }
    return selected;
  }

  get hasSelectedItems() {
    const {
      list
    } = this.state;

    let hasItem = false;
    for (let i = 0, ni = list.length; i < ni; i++) {
      if (list[i].selected) {
        hasItem = true;
        break;
      }
    }
    return hasItem;
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleDeleteSelectedItems() {
    this.setState({
      showDeleteConfirm: false,
      isDeleting: true
    });
    const selectedList = this.state.list.filter((item) => item.selected).map(item => item.id);
    const data = await Api.delete(`admin/manual/poll-pages`, {games: selectedList});
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

    this.setState({
      isDeleting: false
    });
  }

  async handleToggleActiveSelectedItems() {
    this.setState({
      isTogglingActive: true
    });
    const selectedList = this.state.list.filter((item) => item.selected).map(item => item.id);
    const data = await Api.put(`admin/manual/poll-pages/toggle-active`, {games: selectedList});
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
    this.setState({
      isTogglingActive: false
    });
  }

  render() {
    const {
      categories, subcategories, list, isSearching, search, isDeleting, isTogglingActive
    } = this.state;
    return (
      <Page
        title="Manual Polls"
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            categories={categories}
            subcategories={subcategories}
            isSearching={isSearching}
            onSearch={this.handleSearch}
          />
        </Card>
        <Card sectioned>
          <Header
            onToggleSelectAll={this.handleToggleSelectAll}
            isCheckDisabled={list.length === 0}
            checked={this.isSelectedAll}
          />
          {
            list.map((data, index) => (
              <Item
                key={`${index}`}
                data={data}
                subcategories={subcategories}
                onDelete={this.handleDelete}
                onDetail={this.handleDetail}
                onEdit={this.handleEdit}
                onToggleActive={this.handleToggleActive}
                OnToggleSelectItem={this.handleToggleSelectItem}
              />
            ))
          }
          <div className="game-bottom-action">
            <Modal
              open={this.state.showDeleteConfirm}
              onClose={this.toggleDeleteConfirm}
              title="Confirm"
              primaryAction={{
                content: 'Delete',
                onAction: this.handleDeleteSelectedItems
              }}
              secondaryActions={[
                {
                  content: 'Cancel',
                  onAction: this.toggleDeleteConfirm
                }
              ]}
            >
              <Modal.Section>
                <TextContainer>
                  Are you sure you want to delete all the selected polls?
                </TextContainer>
              </Modal.Section>
            </Modal>
            <ButtonGroup segmented>
              <Button
                destructive={search.inactive == 'false'}
                primary={search.inactive == 'true'}
                onClick={this.handleToggleActiveSelectedItems}
                icon={search.inactive == 'true' ? 'checkmark' : 'cancelSmall'}
                disabled={!this.hasSelectedItems || isDeleting}
                loading={isTogglingActive}
              >
                {search.inactive == 'true' ? 'Activate' : 'Deactivate'}
              </Button>
              <Button
                onClick={this.toggleDeleteConfirm}
                icon="delete"
                disabled={!this.hasSelectedItems || isTogglingActive}
                loading={isDeleting}
              >
                Delete
              </Button>
            </ButtonGroup>
          </div>
        </Card>
        <Card sectioned>
          <Add
            subcategories={subcategories}
            onAdd={this.handleAdd}
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Polls));
