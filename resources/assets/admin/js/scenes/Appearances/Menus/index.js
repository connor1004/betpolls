/* eslint-disable no-param-reassign */
/* eslint-disable no-shadow */
import React, { Component } from 'react';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import Sortly, {
  convert, add, remove, flatten
} from 'react-sortly';
import {
  Page, Card, ButtonGroup, Button
} from '@shopify/polaris';
import QueryString from 'qs';

import Api from '../../../apis/app';

import Item from './Item';
import Edit from './Edit';
import { MENU_TYPE_OPTIONS } from '../../../configs/options';
import { MENU_TYPE } from '../../../configs/enums';

class Home extends Component {
  constructor(props) {
    super(props);
    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
    if (!search.menu_type) {
      search.menu_type = MENU_TYPE.HEADER;
    }
    this.state = {
      search,
      leagues: [],
      categories: [],
      items: [],
      selectedIndex: -1
    };

    this.handleSelectItem = this.handleSelectItem.bind(this);
    this.handleEditItem = this.handleEditItem.bind(this);
    this.handleNewItem = this.handleNewItem.bind(this);
    this.handleDeleteItem = this.handleDeleteItem.bind(this);
    this.handleChange = this.handleChange.bind(this);
    this.renderItem = this.renderItem.bind(this);
  }

  async componentDidMount() {
    const {
      search
    } = this.state;
    const responses = await Promise.all([
      Api.get('admin/sports/leagues/all'),
      Api.get('admin/sports/categories'),
      Api.get(`admin/appearances/menus/${search.menu_type}`)
    ]);

    const leagues = responses[0].body;
    const categories = responses[1].body;
    const menus = responses[2].body;
    await this.setState({
      leagues,
      categories,
      items: convert(menus.map(item => this.convertTreeData(item)))
    });
  }

  async componentWillReceiveProps(props) {
    if (this.props.location.search === props.location.search) {
      return;
    }

    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
    if (!search.menu_type) {
      search.menu_type = MENU_TYPE.HEADER;
    }
    await this.setState({
      search
    });
    const responses = await Promise.all([
      Api.get(`admin/appearances/menus/${search.menu_type}`)
    ]);

    const menus = responses[0].body;
    await this.setState({
      items: convert(menus.map(item => this.convertTreeData(item)))
    });
  }

  async handleSearch(params) {
    this.props.history.push(`/admin/appearances/menus${QueryString.stringify(params, { addQueryPrefix: true })}`);
  }

  convertTreeData(menu) {
    menu.parentId = menu.parent_id;
    return menu;
  }

  reconvertTreeData(menu) {
    menu.parent_id = menu.parentId;
    return menu;
  }

  handleChange(items) {
    const menus = (flatten(items)).map(item => this.reconvertTreeData(item));
    Api.post('admin/appearances/menus/reorder', menus);
    this.setState({
      items: convert(menus.map(item => this.convertTreeData(item)))
    });
  }

  async handleSelectItem(index) {
    const {
      items
    } = this.state;
    await this.setState({
      selectedIndex: index
    });
    setTimeout(() => {
      this.setState({
        items: [...items]
      });
    }, 50);
  }

  handleNewItem() {
    this.handleSelectItem(-1);
  }

  async handleEditItem(values, bags) {
    const {
      selectedIndex
    } = this.state;
    if (selectedIndex >= 0) {
      await this.handleUpdateItem(selectedIndex, values, bags);
    } else {
      await this.handleAddItem(values, bags);
    }
  }

  async handleAddItem(values, bags) {
    await bags.setSubmitting(true);
    const {
      items, search
    } = this.state;

    if (values.top_menu === undefined) {
      values.top_menu = 0;
    }

    if (values.burger_menu === undefined) {
      values.burger_menu = 0;
    }

    const {
      body
    } = await Api.post(`admin/appearances/menus/${search.menu_type}`, values);
    await this.setState({
      items: add(items, this.convertTreeData(body))
    });
    await bags.setTouched({
      title: false,
      url: false
    });
    await bags.setSubmitting(false);
    return values;
  }

  async handleUpdateItem(index, values, bags) {
    await bags.setSubmitting(true);
    const {
      items, search
    } = this.state;
    const item = items[index];
    const {
      body
    } = await Api.put(`admin/appearances/menus/${search.menu_type}/${item.id}`, values);

    items[index] = {
      ...item,
      ...this.convertTreeData(body)
    };
    await this.setState({
      items: [...items]
    });
    await bags.setSubmitting(false);
    return values;
  }

  async handleDeleteItem(index) {
    const {
      items
    } = this.state;
    const item = items[index];
    this.setState({
      items: remove(items, index),
      selectedIndex: -1
    });
    await Api.delete(`admin/appearances/menus/${item.id}`);
    return true;
  }

  renderItem(props) {
    const {
      leagues,
      categories
    } = this.state;
    return (
      <Item
        data={props}
        onSelect={this.handleSelectItem}
        onDelete={this.handleDeleteItem}
        categories={categories}
        leagues={leagues}
      />
    );
  }

  render() {
    const {
      items, leagues, categories, selectedIndex, search
    } = this.state;
    return (
      <Page
        title="Menu"
        fullWidth
      >
        <Card sectioned>
          <ButtonGroup sectioned>
            {MENU_TYPE_OPTIONS.map((item, index) => (
              <Button
                key={`${index}`}
                primary={search.menu_type === item.value}
                onClick={this.handleSearch.bind(this, {
                  menu_type: item.value
                })}
              >
                {item.label}
              </Button>
            ))}
          </ButtonGroup>
        </Card>
        <Card sectioned>
          <Sortly
            items={items}
            itemRenderer={this.renderItem}
            onChange={this.handleChange}
          />
        </Card>
        <Edit
          data={selectedIndex >= 0 ? items[selectedIndex] : {}}
          isEditing={selectedIndex > -1}
          onSubmit={this.handleEditItem}
          onNew={this.handleNewItem}
          leagues={leagues}
          categories={categories}
        />
      </Page>
    );
  }
}

export default DragDropContext(HTML5Backend)(Home);
