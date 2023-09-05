/* eslint-disable no-shadow */
import React, { Component, Fragment } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import QueryString from 'qs';
import {
  Page, Spinner
} from '@shopify/polaris';

import Api from '../../../../apis/app';
import View from './View';
import BetTypes from './BetTypes';


class Detail extends Component {
  constructor(props) {
    super(props);
    this.state = {
      game: null
    };
    this.prevSearch = {};

    this.handleUpdate = this.handleUpdate.bind(this);
    this.handleAddBetTypes = this.handleAddBetTypes.bind(this);
    this.handleUpdateBetType = this.handleUpdateBetType.bind(this);
    this.handleGoBack = this.handleGoBack.bind(this);
  }

  async componentDidMount() {
    const {
      params
    } = this.props.match;
    const { body } = await Api.get(`admin/bets/games/${params.id}`);
    this.setState({
      game: body
    });
  }

  async handleUpdate(values, bags) {
    const {
      params
    } = this.props.match;
    const {
      body, response
    } = await Api.put(`admin/bets/games/${params.id}`, values);
    switch (response.status) {
      case 422:
        bags.setStatus(body.error);
        break;
      case 200:
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  async handleAddBetTypes() {
    const {
      params
    } = this.props.match;
    const { response, body } = await Api.post(`admin/bets/games/${params.id}/bet-types`);
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200: {
        const { game } = this.state;
        game.game_bet_types = body;
        this.setState({
          game
        });
        break;
      }
      default:
        break;
    }
  }

  async handleUpdateBetType(values, bags) {
    const {
      params
    } = this.props.match;
    await bags.setSubmitting(true);

    const { response, body } = await Api.post(`admin/bets/games/${params.id}/bet-types/${values.id}`, values);
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 200:
        break;
      default:
        break;
    }

    await bags.setSubmitting(false);
  }

  handleGoBack() {
    this.props.history.goBack();
  }

  render() {
    const {
      game
    } = this.state;
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: 'Games', onAction: this.handleGoBack }]}
        title={game ? `Game Detail: ${game.home_team.name} - ${game.away_team.name}` : 'Game Detail'}
      >
        {
          game ? (
            <Fragment>
              <View
                data={game}
                onUpdate={this.handleUpdate}
              />
              <BetTypes
                game={game}
                data={game.game_bet_types}
                onUpdateItem={this.handleUpdateBetType}
                onCreate={this.handleAddBetTypes}
              />
            </Fragment>
          ) : (
            <Spinner />
          )
        }
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Detail));
