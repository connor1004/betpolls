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
import Import from './Import';

class Game extends Component {
  constructor(props) {
    super(props);
    this.state = {
      leagues: [],
      teams: [],
      list: [],
      search: {},
      isSearching: false,
      showDeleteConfirm: false,
      isDeleting: false,
      isTogglingActive: false
    };
    this.prevSearch = {};

    this.handleSearch = this.handleSearch.bind(this);
    this.handleImport = this.handleImport.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handlePull = this.handlePull.bind(this);
    this.handleToggleSelectItem = this.handleToggleSelectItem.bind(this);
    this.handleToggleSelectAll = this.handleToggleSelectAll.bind(this);
    this.handleDeleteSelectedItems = this.handleDeleteSelectedItems.bind(this);
    this.handleToggleActiveSelectedItems = this.handleToggleActiveSelectedItems.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  async componentDidMount() {
    const {
      body: leagues
    } = await Api.get('admin/sports/leagues/all');
    await this.setState({
      leagues
    });
    this.componentWillReceiveProps(this.props);
  }

  async componentWillReceiveProps(props) {
    const {
      leagues
    } = this.state;
    const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });

    if (!search.league_id) {
      if (leagues.length > 0) {
        search.league_id = leagues[0].id;
      }
    }
    if (!search.start_at) {
      search.start_at = moment().format('YYYY-MM-DD');
      search.end_at = moment().format('YYYY-MM-DD');
    }
    await this.setState({
      search
    });
    this.search();
  }

  get league() {
    const { search, leagues } = this.state;
    if (leagues.length > 0) {
      for (let i = 0, ni = leagues.length; i < ni; i++) {
        const league = leagues[i];
        if (`${search.league_id}` === `${league.id}`) {
          return league;
        }
      }
    }
    return null;
  }

  async handleSearch(search) {
    this.props.history.push(`/admin/bets/games${QueryString.stringify(search, { addQueryPrefix: true })}`);
  }

  async search(isSearching = true) {
    const {
      search
    } = this.state;

    if (this.prevSearch.league_id !== search.league_id) {
      const {
        body
      } = await Api.get(`admin/sports/leagues/${search.league_id}/teams`);
      const teams = OptionsHelper.getCustomOptions(body, value => (`${value.team.id}`), value => (value.team.name));
      await this.setState({
        teams
      });
    }
    await this.setState({
      isSearching
    });

    const {
      body, response
    } = await Api.get('admin/bets/games', search);
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

  async handleImport() {
    const {
      search, list
    } = this.state;
    const { league } = this;
    this.props.history.push(`/admin/bets/games/import${QueryString.stringify(search, { addQueryPrefix: true })}`, {
      league,
      search,
      list
    });
  }

  async handleAdd(values, bags, components) {
    const {
      search, list
    } = this.state;
    const {
      body, response
    } = await Api.post('admin/bets/games', { ...values, league_id: search.league_id });
    switch (response.status) {
      case 422:
        bags.setStatus(body.error);
        break;
      case 200:
        await bags.setValues(components.paramsToFormValues({}));
        await bags.setTouched({});
        this.setState({
          list: [
            ...list,
            body
          ]
        });
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  async handleToggleActive(id) {
    const data = await Api.put(`admin/bets/games/${id}/toggle-active`);
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
    const data = await Api.delete(`admin/bets/games/${id}`);
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
    window.open(`/admin/bets/games/${data.id}`, '_blank');
    // this.props.history.push(`/admin/bets/games/${data.id}`, {
    //   data
    // });
  }

  async handlePull(id, component) {
    component.setPulling(true);
    const data = await Api.put(`admin/bets/games/${id}/pull`);
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
    component.setPulling(false);
    return data;
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
    const data = await Api.delete(`admin/bets/games`, {games: selectedList});
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
    const data = await Api.put(`admin/bets/games/toggle-active`, {games: selectedList});
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
      leagues, list, isSearching, search, teams, isDeleting, isTogglingActive
    } = this.state;
    const { league } = this;
    return (
      <Page
        title="Games"
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            leagues={leagues}
            isSearching={isSearching}
            onSearch={this.handleSearch}
            onImport={this.handleImport}
          />
        </Card>
        <Card sectioned>
          <Header
            homeTeamFirst={league && league.sport_category && league.sport_category.home_team_first}
            onToggleSelectAll={this.handleToggleSelectAll}
            between_players={league && league.sport_category && league.sport_category.between_players}
            isCheckDisabled={list.length === 0}
            checked={this.isSelectedAll}
          />
          {
            list.map((data, index) => (
              <Item
                homeTeamFirst={data.between_players || (league && league.sport_category && league.sport_category.home_team_first)}
                key={`${index}`}
                data={data}
                onDelete={this.handleDelete}
                onPull={this.handlePull}
                onDetail={this.handleDetail}
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
                  Are you sure you want to delete all the selected games?
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
            homeTeamFirst={league && league.sport_category && league.sport_category.home_team_first}
            between_players={league && league.sport_category && league.sport_category.between_players}
            teams={teams}
            onAdd={this.handleAdd}
          />
        </Card>
        <Route path="/admin/bets/games/import" name="Games" component={Import} />
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Game));
